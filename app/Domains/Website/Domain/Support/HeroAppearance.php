<?php

declare(strict_types=1);

namespace App\Domains\Website\Domain\Support;

/** Valores iniciais e limites da apresentação do banner. Cores vazias herdam o tema. */
final class HeroAppearance
{
    public const RANGES = [
        'title_size' => [24, 72, 58],
        'subtitle_size' => [14, 32, 18],
        'badge_radius' => [0, 60, 40],
        'button_radius' => [0, 60, 40],
        'button_shadow' => [0, 40, 0],
        'overlay_opacity' => [0, 100, 75],
        'gradient_end_opacity' => [0, 100, 30],
        'height' => [240, 900, 480],
        'content_width' => [320, 1160, 820],
        'interval' => [3, 20, 6],
    ];

    public const COLORS = [
        'background_color', 'title_color', 'subtitle_color', 'badge_color',
        'badge_background', 'button_color', 'button_background', 'overlay_color', 'gradient_end_color',
    ];

    public const CHOICES = [
        'mode' => ['text', 'banners'],
        'title_align' => ['left', 'center', 'right'],
        'subtitle_align' => ['left', 'center', 'right'],
        'badge_align' => ['left', 'center', 'right'],
        'button_align' => ['left', 'center', 'right'],
        'gradient_direction' => ['to bottom', 'to top', 'to right', 'to left'],
        'image_fit' => ['cover', 'contain'],
        'image_position' => ['center', 'top', 'bottom', 'left', 'right'],
    ];

    public const TOGGLES = ['badge_visible' => true, 'gradient_enabled' => true, 'autoplay' => true];

    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        $values = ['badge_text' => ''];

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

        $values['badge_text'] = trim((string) ($input['badge_text'] ?? ''));

        return $values;
    }
}
