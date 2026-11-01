<?php

declare(strict_types=1);

namespace App\Domains\Website\Domain\Support;

/** Aparência da seção de apresentação do núcleo, com cores herdadas do tema. */
final class AboutAppearance
{
    public const RANGES = [
        'title_size' => [20, 56, 35],
        'subtitle_size' => [14, 28, 17],
        'body_size' => [14, 26, 17],
        'body_line_height' => [140, 220, 160],
        'image_width' => [25, 60, 45],
        'image_height' => [160, 700, 420],
        'image_radius' => [0, 60, 20],
        'image_shadow' => [0, 40, 20],
        'button_radius' => [0, 60, 40],
        'button_shadow' => [0, 40, 0],
        'padding_vertical' => [24, 120, 72],
        'gap' => [16, 80, 48],
    ];

    public const COLORS = [
        'title_color', 'subtitle_color', 'body_color', 'button_color',
        'button_background', 'background_color', 'divider_color', 'gradient_end_color',
    ];

    public const CHOICES = [
        'title_align' => ['left', 'center', 'right'],
        'subtitle_align' => ['left', 'center', 'right'],
        'body_align' => ['left', 'center', 'right', 'justify'],
        'button_align' => ['left', 'center', 'right'],
        'image_side' => ['right', 'left', 'top', 'bottom'],
        'image_fit' => ['cover', 'contain'],
        'image_position' => ['center', 'top', 'bottom', 'left', 'right'],
        'gradient_direction' => ['to bottom', 'to top', 'to right', 'to left'],
    ];

    public const TOGGLES = ['image_visible' => true, 'title_divider' => true, 'gradient_enabled' => false];

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
