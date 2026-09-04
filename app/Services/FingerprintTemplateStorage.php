<?php

namespace App\Services;

use Illuminate\Contracts\Encryption\DecryptException;


class FingerprintTemplateStorage
{
    
    public static function packForStorage(string $template, string $modelVersion = 'zkfinger_v2.1.24', string $algorithmVersion = 'zkalg12'): array
    {
        return [
            'enc' => base64_encode(encrypt($template)),
            'model' => $modelVersion,
            'algorithm' => $algorithmVersion,
            'template_size' => strlen($template),
        ];
    }

    public static function decodeFromStorage(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        if (!is_array($value)) {
            return null;
        }

        if (!isset($value['enc']) || !is_string($value['enc'])) {
            return null;
        }

        try {
            $decrypted = decrypt(base64_decode($value['enc']));
            return $decrypted;
        } catch (DecryptException $e) {
            return null;
        }
    }
    public static function hasEnrollment(mixed $raw): bool
    {
        return self::decodeFromStorage($raw) !== null;
    }
    public static function getMetadata(mixed $raw): ?array
    {
        if ($raw === null || $raw === '') {
            return null;
        }

        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }

        if (!is_array($raw)) {
            return null;
        }

        return [
            'model' => $raw['model'] ?? null,
            'algorithm' => $raw['algorithm'] ?? null,
            'template_size' => $raw['template_size'] ?? null,
        ];
    }
}
