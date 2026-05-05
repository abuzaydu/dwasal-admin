<?php

namespace App\Services\Firebase;

use Firebase\JWT\JWT;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;

class FcmClient
{
    private $httpClient;

    private array $serviceAccount;

    public function __construct()
    {
        $httpOptions = [
            'timeout' => 20,
            'connect_timeout' => 10,
        ];

        // Local WAMP environments commonly miss CA bundle config.
        // Fallback disables TLS verification only for local environment.
        if (app()->environment('local')) {
            $httpOptions['verify'] = false;
            \Log::warning('FCM client is running with SSL verification disabled in local environment.');
        }

        $this->httpClient = new HttpClient($httpOptions);

        $credentialsPath = storage_path('app/firebase/dwasal_firebase_credentials.json');
        $decoded = json_decode(file_get_contents($credentialsPath), true);
        $this->serviceAccount = is_array($decoded) ? $decoded : [];
    }

    public function sendMessage($token, $notification)
    {
        if (empty($token)) {
            \Log::warning('FCM send skipped: empty token');
            return ['error' => 'Empty FCM token'];
        }

        $title = $notification['title'] ?? null;
        $body = $notification['body'] ?? null;
        $data = $notification['data'] ?? [];
        if (! is_array($data)) {
            $data = [];
        }

        // Use same Firebase project as the mobile app (dwasal-1b0cf from google-services.json)
        $projectId = $this->serviceAccount['project_id'] ?? 'dwasal-1b0cf';

        try {
            $tokenResponse = $this->fetchAccessToken();
            $accessToken = $tokenResponse['access_token'] ?? null;
            if (! $accessToken) {
                \Log::error('Failed to retrieve access token', $tokenResponse);
                throw new \Exception('Failed to retrieve access token');
            }
        } catch (\Exception $e) {
            \Log::error('Error fetching access token: '.$e->getMessage());

            return ['error' => 'Could not fetch access token'];
        }

        $fcmUrl = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => (string) ($title ?? 'Dwasal'),
                    'body' => (string) ($body ?? ''),
                ],
                // FCM data values must be strings.
                'data' => array_map('strval', $data),
                'android' => [
                    'priority' => 'high',
                ],
            ],
        ];

        try {
            $response = $this->httpClient->post($fcmUrl, [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            return json_decode((string) $response->getBody(), true);
        } catch (RequestException $e) {
            $rawBody = (string) optional($e->getResponse())->getBody();
            $decoded = json_decode($rawBody, true);
            $errorMessage = $decoded['error']['message'] ?? $e->getMessage();
            $errorStatus = $decoded['error']['status'] ?? null;

            \Log::error('Error sending FCM message: '.$errorMessage, [
                'status' => $errorStatus,
                'response' => $decoded,
            ]);

            return [
                'error' => $errorMessage,
                'status' => $errorStatus,
            ];
        } catch (\Exception $e) {
            \Log::error('Error sending FCM message: '.$e->getMessage());
            return ['error' => 'Failed to send message'];
        }
    }

    private function fetchAccessToken(): array
    {
        $clientEmail = $this->serviceAccount['client_email'] ?? null;
        $privateKey = $this->serviceAccount['private_key'] ?? null;
        $tokenUri = $this->serviceAccount['token_uri'] ?? 'https://oauth2.googleapis.com/token';

        if (! $clientEmail || ! $privateKey) {
            throw new \Exception('Invalid Firebase service account credentials');
        }

        $now = time();
        $assertion = JWT::encode([
            'iss' => $clientEmail,
            'sub' => $clientEmail,
            'aud' => $tokenUri,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'iat' => $now,
            'exp' => $now + 3600,
        ], $privateKey, 'RS256');

        $response = $this->httpClient->post($tokenUri, [
            'form_params' => [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $assertion,
            ],
        ]);

        return json_decode((string) $response->getBody(), true) ?? [];
    }
}