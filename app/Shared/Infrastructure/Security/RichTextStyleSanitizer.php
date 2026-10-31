<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Security;

/** Preserva somente valores CSS de apresentação usados pelo editor de texto rico. */
final class RichTextStyleSanitizer
{
    public static function sanitize(string $style): string
    {
        $clean = [];

        foreach (explode(';', $style) as $declaration) {
            $parts = explode(':', $declaration, 2);

            if (count($parts) !== 2) {
                continue;
            }

            $property = strtolower(trim($parts[0]));
            $value = trim($parts[1]);

            if ($value === '' || preg_match('/[\\\\{}<>\x00-\x1f\x7f]|\/\*|\*\//', $value)) {
                continue;
            }

            if (self::allows($property, $value)) {
                $clean[$property] = $value;
            }
        }

        return implode('; ', array_map(
            static fn (string $property, string $value): string => $property.': '.$value,
            array_keys($clean),
            array_values($clean),
        ));
    }

    public static function isFontFamily(string $value): bool
    {
        return strlen($value) <= 250
            && preg_match('/^(?:[a-z0-9 -]+|"[a-z0-9 -]+"|\'[a-z0-9 -]+\')(?:\s*,\s*(?:[a-z0-9 -]+|"[a-z0-9 -]+"|\'[a-z0-9 -]+\'))*$/iD', $value) === 1;
    }

    public static function isColor(string $value): bool
    {
        if (preg_match('/^(?:[a-z]{1,32}|#[a-f0-9]{3}|#[a-f0-9]{4}|#[a-f0-9]{6}|#[a-f0-9]{8})$/iD', $value)) {
            return true;
        }

        if (! preg_match('/^(rgb|rgba|hsl|hsla)\(([^()]+)\)$/iD', $value, $match)) {
            return false;
        }

        $function = strtolower($match[1]);
        $parts = array_map('trim', explode(',', $match[2]));
        $hasAlpha = in_array($function, ['rgba', 'hsla'], true);

        if (count($parts) !== ($hasAlpha ? 4 : 3)) {
            return false;
        }

        if ($hasAlpha) {
            $alpha = array_pop($parts);

            if (! preg_match('/^(?:0(?:\.\d+)?|1(?:\.0+)?|\.\d+)$/D', $alpha)) {
                return false;
            }
        }

        foreach ($parts as $index => $part) {
            if (! preg_match('/^\d+(?:\.\d+)?%?$/D', $part)) {
                return false;
            }

            $percent = str_ends_with($part, '%');

            if (str_starts_with($function, 'hsl')) {
                if (($index > 0 && ! $percent) || ($index === 0 && $percent)) {
                    return false;
                }

                $maximum = $index === 0 ? 360 : 100;
            } else {
                $maximum = $percent ? 100 : 255;
            }

            if ((float) $part > $maximum) {
                return false;
            }
        }

        return true;
    }

    private static function allows(string $property, string $value): bool
    {
        $lower = strtolower($value);

        return match ($property) {
            'color', 'background-color' => self::isColor($value),
            'font-family' => self::isFontFamily($value),
            'font-size' => self::isLength($value),
            'font-weight' => preg_match('/^(?:normal|bold|bolder|lighter|[1-9]00)$/iD', $value) === 1,
            'font-style' => in_array($lower, ['normal', 'italic', 'oblique'], true),
            'text-decoration', 'text-decoration-line' => preg_match('/^(?:none|(?:underline|line-through|overline)(?:\s+(?:underline|line-through|overline))*)$/iD', $value) === 1,
            'text-align' => in_array($lower, ['left', 'right', 'center', 'justify', 'start', 'end'], true),
            'vertical-align' => in_array($lower, ['baseline', 'sub', 'super', 'top', 'middle', 'bottom', 'text-top', 'text-bottom'], true),
            'line-height' => $lower === 'normal' || self::isLength($value)
                || (is_numeric($value) && (float) $value > 0 && (float) $value <= 10),
            'text-indent', 'margin-left', 'margin-right', 'margin-top', 'margin-bottom',
            'padding-left', 'padding-right', 'padding-top', 'padding-bottom' => $lower === 'auto' || self::isLength($value),
            'margin', 'padding' => self::isSpacing($value),
            'width', 'height', 'max-width', 'max-height' => $lower === 'auto' || self::isLength($value),
            'float' => in_array($lower, ['left', 'right', 'none'], true),
            'display' => in_array($lower, ['inline', 'block', 'inline-block'], true),
            'border-collapse' => in_array($lower, ['collapse', 'separate'], true),
            'border', 'border-top', 'border-right', 'border-bottom', 'border-left' => self::isBorder($value),
            'border-width', 'border-spacing' => self::isSpacing($value),
            'border-style' => in_array($lower, ['none', 'solid', 'dashed', 'dotted', 'double'], true),
            'border-color' => self::isColor($value),
            'border-radius' => self::isLength($value),
            'list-style-type' => in_array($lower, ['disc', 'circle', 'square', 'decimal', 'lower-alpha', 'upper-alpha', 'lower-roman', 'upper-roman'], true),
            default => false,
        };
    }

    private static function isLength(string $value): bool
    {
        if ($value === '0') {
            return true;
        }

        return preg_match('/^(\d+(?:\.\d+)?|\.\d+)(?:px|pt|em|rem|%)$/iD', $value, $match) === 1
            && (float) $match[1] <= 10000;
    }

    private static function isSpacing(string $value): bool
    {
        $parts = preg_split('/\s+/', trim($value));

        if (count($parts) > 4) {
            return false;
        }

        foreach ($parts as $part) {
            if ($part !== 'auto' && ! self::isLength($part)) {
                return false;
            }
        }

        return true;
    }

    private static function isBorder(string $value): bool
    {
        if (in_array(strtolower($value), ['none', '0'], true)) {
            return true;
        }

        $parts = preg_split('/\s+/', $value, 3);

        return count($parts) === 3
            && self::isLength($parts[0])
            && in_array(strtolower($parts[1]), ['solid', 'dashed', 'dotted', 'double'], true)
            && self::isColor($parts[2]);
    }
}
