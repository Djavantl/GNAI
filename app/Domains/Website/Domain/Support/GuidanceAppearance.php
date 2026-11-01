<?php

declare(strict_types=1);

namespace App\Domains\Website\Domain\Support;

/** Apresentação das etapas e da ação de orientação. */
final class GuidanceAppearance
{
    public const RANGES = [
        'title_size' => [20, 56, 35],
        'subtitle_size' => [14, 28, 17],
        'padding_vertical' => [24, 120, 72],
        'gap' => [8, 64, 28],
        'columns' => [1, 4, 3],
        'interval' => [3, 20, 6],
        'button_size' => [14, 24, 16],
        'button_radius' => [0, 60, 40],
        'button_shadow' => [0, 40, 0],
        'hover_lift' => [0, 8, 0],
        'hover_shadow' => [0, 40, 0],
    ];
    public const COLORS = [
        'title_color', 'subtitle_color', 'divider_color', 'background_color',
        'gradient_end_color', 'timeline_color', 'button_color', 'button_background',
        'hover_color', 'hover_background',
    ];
    public const CHOICES = [
        'title_align' => ['left', 'center', 'right'],
        'subtitle_align' => ['left', 'center', 'right'],
        'layout' => ['grid', 'list', 'timeline', 'carousel'],
        'button_align' => ['left', 'center', 'right'],
        'action_style' => ['link', 'button'],
        'gradient_direction' => ['to bottom', 'to top', 'to right', 'to left'],
    ];
    public const TOGGLES = [
        'title_divider' => true, 'gradient_enabled' => false,
        'autoplay' => false, 'hover_enabled' => true, 'button_icon' => true,
    ];

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
