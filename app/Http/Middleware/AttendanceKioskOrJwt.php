<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class AttendanceKioskOrJwt
{
    /**
     * Authorize attendance APIs with a per-company COMPANY_API_KEY.
     * A valid user JWT is still accepted so already-logged-in app sessions keep working.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $sentKey = $this->extractCompanyApiKey($request);

        if ($sentKey !== '') {
            $company = Company::query()->where('company_api_key', $sentKey)->first();
            if ($company && hash_equals((string) $company->company_api_key, $sentKey)) {
                $this->bindCompany($request, $company);
                if (!$this->jwtMatchesBoundCompany($request)) {
                    return response()->json([
                        'message' => 'This user is not a member of the company for this API key',
                    ], 403);
                }

                return $next($request);
            }

            return response()->json([
                'message' => 'Invalid company API key',
            ], 401);
        }

        if ($this->tryAuthenticateJwt($request)) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Unauthenticated',
            'hint' => 'Send header X-Company-Api-Key (or COMPANY_API_KEY) with this company\'s unique key.',
        ], 401);
    }

    private function extractCompanyApiKey(Request $request): string
    {
        $candidates = [
            $request->header('X-Company-Api-Key'),
            $request->header('COMPANY_API_KEY'),
            $request->header('Company-Api-Key'),
            $request->header('X-Attendance-Kiosk-Key'),
            $request->input('company_api_key'),
        ];

        foreach ($candidates as $value) {
            $key = trim((string) $value);
            if ($key !== '') {
                return $key;
            }
        }

        $auth = trim((string) $request->header('Authorization', ''));
        if (stripos($auth, 'ApiKey ') === 0) {
            return trim(substr($auth, 7));
        }

        return '';
    }

    private function bindCompany(Request $request, Company $company): void
    {
        $request->attributes->set('attendance_company', $company);
        $request->attributes->set('attendance_company_id', (int) $company->id);
    }

    private function tryAuthenticateJwt(Request $request): bool
    {
        try {
            return (bool) JWTAuth::parseToken()->authenticate();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * When a JWT is also present, it must belong to the company identified by the API key.
     * Missing/expired JWT is allowed — the company key is enough for kiosk attendance.
     */
    private function jwtMatchesBoundCompany(Request $request): bool
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return true;
            }
            $company = $request->attributes->get('attendance_company');
            if (!$company instanceof Company) {
                return true;
            }

            return $user->companies()->where('companies.id', $company->id)->exists();
        } catch (\Throwable $e) {
            return true;
        }
    }
}
