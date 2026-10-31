<?php

declare(strict_types=1);

namespace App\Domains\Settings\Domain\Models;

use App\Domains\Settings\Domain\Enums\SettingCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

final class Setting extends Model
{
    protected $fillable = [
        'category',
        'payload',
    ];

    protected $casts = [
        'category' => SettingCategory::class,
        'payload' => 'array',
    ];

    /**
     * Retorna os valores salvos da categoria, ou null quando ainda não foram
     * cadastrados ou a tabela ainda não existe (ambiente sem migrations).
     *
     * @return array<string, mixed>|null
     */
    public static function payloadFor(SettingCategory $category): ?array
    {
        try {
            $setting = self::query()
                ->where('category', $category->value)
                ->first();
        } catch (QueryException) {
            return null;
        }

        return is_array($setting?->payload) ? $setting->payload : null;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function store(SettingCategory $category, array $payload): self
    {
        return self::query()->updateOrCreate(
            ['category' => $category->value],
            ['payload' => $payload],
        );
    }
}
