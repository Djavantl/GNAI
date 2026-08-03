<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\People\Concerns;

trait NormalizesPersonInput
{
    public static function prepareForPipeline(array $properties): array
    {
        foreach (['name', 'email', 'registration'] as $field) {
            if (array_key_exists($field, $properties) && is_string($properties[$field])) {
                $properties[$field] = trim($properties[$field]);
            }
        }

        foreach (['document', 'phone'] as $field) {
            if (array_key_exists($field, $properties)) {
                $properties[$field] = self::normalizeOptionalDigits($properties[$field]);
            }
        }

        foreach (['is_repeater', 'remove_photo', 'is_admin'] as $field) {
            if (array_key_exists($field, $properties)) {
                $properties[$field] = filter_var(
                    $properties[$field],
                    FILTER_VALIDATE_BOOLEAN,
                );
            }
        }

        return $properties;
    }

    private static function normalizeOptionalDigits(mixed $value): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $value) ?? '';

        return $digits === '' ? null : $digits;
    }
}
