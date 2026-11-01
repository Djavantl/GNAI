<?php

declare(strict_types=1);

namespace App\Domains\Website\Domain\Support;

/** Contrato compartilhado das seções institucionais organizadas em cards. */
final class PillarsAppearance
{
    public const SECTION_TYPES = ['pillars', 'audience', 'features', 'faq', 'resources'];

    public const RANGES = [
        'title_size' => [20, 56, 35],
        'subtitle_size' => [14, 28, 17],
        'padding_vertical' => [24, 120, 72],
        'gap' => [8, 48, 24],
        'columns' => [1, 4, 4],
    ];
    public const COLORS = ['title_color', 'subtitle_color', 'background_color', 'gradient_end_color', 'divider_color'];
    public const CHOICES = [
        'title_align' => ['center', 'left', 'right'],
        'subtitle_align' => ['center', 'left', 'right'],
        'layout' => ['grid', 'list'],
        'gradient_direction' => ['to bottom', 'to top', 'to right', 'to left'],
    ];
    public const TOGGLES = ['title_divider' => true, 'gradient_enabled' => false];

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
    public static function normalize(array $input, string $sectionType = 'pillars'): array
    {
        $input = [
            'columns' => match ($sectionType) {
                'faq' => 1,
                'audience', 'resources' => 3,
                default => 4,
            },
            ...($sectionType === 'faq' ? ['layout' => 'list'] : []),
            ...$input,
        ];
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
