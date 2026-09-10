<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class FingerprintEnrollmentSetting
{
    private const FILE = 'fingerprint_enrollment.json';

    public static function isAllowed(int $companyId): bool
    {
        $all = self::read();
        $key = (string) $companyId;
        if (!array_key_exists($key, $all)) {
            return true;
        }

        return (bool) $all[$key];
    }

    public static function setAllowed(int $companyId, bool $allowed): void
    {
        $all = self::read();
        $all[(string) $companyId] = $allowed;
        Storage::disk('local')->put(self::FILE, json_encode($all));
    }

    private static function read(): array
    {
        if (!Storage::disk('local')->exists(self::FILE)) {
            return [];
        }

        $raw = json_decode(Storage::disk('local')->get(self::FILE), true);

        return is_array($raw) ? $raw : [];
    }
}
