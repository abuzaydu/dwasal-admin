<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Log;
use Symfony\Component\HttpFoundation\Response;

class AttendanceKioskOrJwt
{
    /**
     * Allow attendance APIs when either:
     * - X-Attendance-Kiosk-Key matches ATTENDANCE_KIOSK_KEY (.env), or
     * - Authorization: Bearer <valid JWT> (user logged in on the app).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('app.attendance_kiosk_key', '');
        $sent = (string) $request->header('X-Attendance-Kiosk-Key', '');

        if ($expected !== '' && $sent !== '' && hash_equals($expected, $sent)) {
            return $next($request);
        }

        try {
            if (JWTAuth::parseToken()->authenticate()) {
                return $next($request);
            }
        } catch (\Throwable $e) {
            // Invalid / missing token
        }

        Log::warning('Unauthenticated attendance request', [
            'path' => $request->path(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'kiosk_key_present' => $sent !== '',
        ]);

        return response()->json([
            'message' => 'Unauthenticated',
            'hint' => 'Log in on the app (JWT), or set ATTENDANCE_KIOSK_KEY in .env and X-Attendance-Kiosk-Key in the app to match.',
        ], 401);
    }
}
