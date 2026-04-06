<?php

namespace App\Audit\Formatters;

abstract class AuditFormatter
{
    abstract protected function formatters(): array;

    public function format(string $field, mixed $value): ?string
    {
        $formatters = $this->formatters();

        if (!isset($formatters[$field])) {
            return null;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = $decoded;
            }
        }

        $formatter = $formatters[$field];

        if (is_string($formatter) && enum_exists($formatter)) {
            return $formatter::from($value)->label();
        }

        if (is_callable($formatter)) {
            return $formatter($value);
        }

        return null;
    }

}
