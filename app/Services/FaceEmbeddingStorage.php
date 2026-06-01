<?php

namespace App\Services;

use Illuminate\Contracts\Encryption\DecryptException;

/**
 * Face embeddings stored in employees.face_embedding (MySQL JSON column).
 * Must always be valid JSON — use wrapped object for encrypted payloads.
 */
class FaceEmbeddingStorage
{
    /**
     * @param array<int, float> $embedding
     * @return array<string, mixed> Valid JSON object for DB column
     */
    public static function packForStorage(array $embedding): array
    {
        $normalized = self::normalizeVector($embedding);
        return self::packTemplatesForStorage([$normalized]);
    }

    /**
     * Store multiple enrollment templates (phone-style: several faces per employee).
     *
     * @param list<array<int, float|int|string>> $templates
     */
    public static function packTemplatesForStorage(array $templates): array
    {
        $packed = [];
        foreach ($templates as $template) {
            if (!is_array($template)) {
                continue;
            }
            $normalized = self::normalizeVector($template);
            if (!empty($normalized)) {
                $packed[] = $normalized;
            }
        }

        if (empty($packed)) {
            return [];
        }

        return [
            'enc' => base64_encode(encrypt(json_encode($packed))),
            'model' => 'facenet',
            'template_count' => count($packed),
        ];
    }

    /**
     * @return array|null Decoded single vector or multi-template structure
     */
    public static function decodeFromStorage(mixed $value): ?array
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

        if (isset($value['enc']) && is_string($value['enc'])) {
            $enc = $value['enc'];
            foreach ([$enc, base64_decode($enc, true) ?: null] as $payload) {
                if (!is_string($payload) || $payload === '') {
                    continue;
                }
                try {
                    $decrypted = decrypt($payload);
                    $decoded = json_decode($decrypted, true);

                    if (is_array($decoded)) {
                        return $decoded;
                    }
                } catch (DecryptException|\Throwable) {
                    // Try next decode strategy.
                }
            }

            return null;
        }

        // Legacy: plain vector or multi-template stored directly in JSON
        if (isset($value[0]) && is_numeric($value[0])) {
            return $value;
        }

        if (isset($value[0]) && is_array($value[0])) {
            return $value;
        }

        // Legacy: encrypted string saved incorrectly (pre-fix)
        if (isset($value[0]) && is_string($value[0])) {
            try {
                $decrypted = decrypt($value[0]);
                $decoded = json_decode($decrypted, true);

                return is_array($decoded) ? $decoded : null;
            } catch (DecryptException) {
                return null;
            }
        }

        return null;
    }

    /**
     * @return list<array<float>> Normalized template vectors (one or many per employee)
     */
    public static function templatesFromStored(mixed $raw): array
    {
        $data = self::decodeFromStorage($raw);
        if (empty($data)) {
            return [];
        }

        if (self::isMultiTemplate($data)) {
            $templates = [];
            foreach ($data as $template) {
                if (!is_array($template)) {
                    continue;
                }
                $normalized = self::normalizeVector($template);
                if (!empty($normalized)) {
                    $templates[] = $normalized;
                }
            }

            return $templates;
        }

        $normalized = self::normalizeVector($data);
        return empty($normalized) ? [] : [$normalized];
    }

    public static function hasEnrollment(mixed $raw): bool
    {
        return count(self::templatesFromStored($raw)) > 0;
    }

    /**
     * Reject enrollment when samples are not the same person (cosine distance).
     *
     * @param list<array<int, float>> $templates Normalized vectors
     */
    public static function templatesAreConsistent(array $templates, float $maxCosineDistance = 0.32): bool
    {
        $count = count($templates);
        if ($count < 2) {
            return true;
        }

        $maxDistance = 0.0;
        for ($i = 0; $i < $count - 1; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $dot = 0.0;
                $a = $templates[$i];
                $b = $templates[$j];
                $len = min(count($a), count($b));
                for ($k = 0; $k < $len; $k++) {
                    $dot += $a[$k] * $b[$k];
                }
                $maxDistance = max($maxDistance, 1.0 - $dot);
            }
        }

        return $maxDistance <= $maxCosineDistance;
    }

    /**
     * @param array<int, float|int|string> $embedding
     * @return array<int, float>
     */
    public static function normalizeVector(array $embedding): array
    {
        if (empty($embedding)) {
            return [];
        }

        $vector = array_map(fn ($v) => (float) $v, $embedding);
        $sumSquares = 0.0;
        foreach ($vector as $value) {
            $sumSquares += ($value * $value);
        }

        $norm = sqrt($sumSquares);
        if ($norm <= 0.0) {
            return [];
        }

        return array_map(fn ($value) => $value / $norm, $vector);
    }

    /**
     * @param array<int, mixed> $data
     */
    private static function isMultiTemplate(array $data): bool
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            return false;
        }

        return isset($data[0][0]) && is_numeric($data[0][0]);
    }
}
