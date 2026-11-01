<?php

declare(strict_types=1);

namespace App\Domains\Website\Domain\Support;

/** Aparência da seção de equipe e dos cards de profissionais. */
final class TeamAppearance
{
    public const RANGES = [
        'photo_size' => [64, 240, 128],
        'photo_radius' => [0, 120, 64],
        'photo_border_width' => [0, 12, 4],
        'card_radius' => [0, 60, 16],
        'card_padding' => [8, 48, 24],
        'card_title_size' => [16, 32, 17],
        'card_text_size' => [14, 24, 14],
        'title_size' => [20, 56, 35],
        'subtitle_size' => [14, 28, 17],
        'padding_vertical' => [24, 120, 72],
        'gap' => [8, 64, 28],
        'columns' => [1, 4, 4],
        'interval' => [3, 20, 6],
        'button_size' => [14, 24, 16],
        'button_radius' => [0, 60, 40],
        'button_shadow' => [0, 40, 0],
        'hover_lift' => [0, 8, 0],
        'hover_shadow' => [0, 40, 0],
    ];
    public const COLORS = [
        'card_background', 'card_title_color', 'card_text_color', 'card_border_color', 'photo_border_color',
        'title_color', 'subtitle_color', 'divider_color', 'background_color',
        'gradient_end_color', 'timeline_color', 'button_color', 'button_background',
        'hover_color', 'hover_background',
    ];
    public const CHOICES = [
        'card_align' => ['center', 'left', 'right'],
        'title_align' => ['left', 'center', 'right'],
        'subtitle_align' => ['left', 'center', 'right'],
        'layout' => ['grid', 'list', 'carousel'],
        'button_align' => ['left', 'center', 'right'],
        'action_style' => ['link', 'button'],
        'gradient_direction' => ['to bottom', 'to top', 'to right', 'to left'],
    ];
    public const TOGGLES = [
        'show_photo' => true, 'show_role' => true,
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
