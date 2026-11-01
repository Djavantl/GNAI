<?php

declare(strict_types=1);

namespace App\Domains\Website\Domain\Support;

final class PillarCardAppearance
{

    public const RANGES = [
        'icon_size' => [12, 64, 24],
        'icon_width' => [24, 120, 52],
        'icon_height' => [24, 120, 52],
        'icon_radius' => [0, 60, 14],
        'icon_gap' => [4, 40, 16],
        'title_size' => [14, 32, 18],
        'text_size' => [12, 24, 16],
        'text_line_height' => [140, 220, 160],
        'card_radius' => [0, 60, 14],
        'card_padding' => [8, 48, 28],
        'card_border_width' => [0, 4, 1],
        'card_shadow' => [0, 40, 0],
        'card_min_height' => [0, 600, 0],
        'card_width' => [50, 100, 100],
        'hover_lift' => [0, 12, 4],
        'hover_shadow' => [0, 40, 20],
    ];
    public const COLORS = [
        'icon_color', 'icon_background', 'title_color', 'text_color',
        'card_background', 'card_border_color', 'hover_background', 'hover_border_color', 'gradient_end_color',
    ];
    public const CHOICES = [
        'icon_align' => ['left', 'center', 'right'],
        'title_align' => ['left', 'center', 'right'],
        'text_align' => ['left', 'center', 'right', 'justify'],
        'icon_position' => ['top', 'left', 'right'],
        'gradient_direction' => ['to bottom', 'to top', 'to right', 'to left'],
    ];
    public const TOGGLES = ['hover_enabled' => false, 'gradient_enabled' => false];

    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        $values = [];

        foreach (self::RANGES as $key => [, , $default]) {
            $values[$key] = $default;
        }

        foreach (self::COLORS as $key) {
            $values[$key] = '';
        }

        foreach (self::CHOICES as $key => $options) {
            $values[$key] = $options[0];
        }

        return [...$values, ...self::TOGGLES];
    }

    /** @return array<string, mixed> */
    public static function normalize(array $input): array
    {
        $values = self::defaults();

        foreach (self::RANGES as $key => [$min, $max, $default]) {
            $values[$key] = max($min, min($max, (int) ($input[$key] ?? $default)));
        }

        foreach (self::COLORS as $key) {
            $color = (string) ($input[$key] ?? '');
            $values[$key] = preg_match('/^#[0-9a-fA-F]{6}$/', $color) ? strtolower($color) : '';
        }

        foreach (self::CHOICES as $key => $options) {
            $values[$key] = in_array($input[$key] ?? null, $options, true) ? $input[$key] : $options[0];
        }

        foreach (self::TOGGLES as $key => $default) {
            $values[$key] = filter_var($input[$key] ?? $default, FILTER_VALIDATE_BOOL);
        }

        return $values;
    }
}
