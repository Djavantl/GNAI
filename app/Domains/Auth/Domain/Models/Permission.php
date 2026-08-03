<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain\Models;

use App\Domains\Auth\Domain\DTOs\Permissions\PermissionDTO;
use App\Domains\Auth\Domain\Exceptions\InvalidPermission;
use Illuminate\Database\Eloquent\Model;

final class Permission extends Model
{
    protected $fillable = ['name', 'slug'];

    /** @throws InvalidPermission */
    public static function register(PermissionDTO $data): self
    {
        return new self([
            'name' => self::normalizeName($data->name),
            'slug' => self::normalizeSlug($data->slug),
        ]);
    }

    /**
     * Completa o nome de permissões antigas sem alterar seu identificador.
     *
     * @throws InvalidPermission
     */
    public function synchronize(PermissionDTO $data): bool
    {
        $slug = self::normalizeSlug($data->slug);

        if ($this->slug !== $slug) {
            throw new InvalidPermission(
                'O identificador de uma permissão não pode ser alterado.'
            );
        }

        if (filled($this->name)) {
            return false;
        }

        $this->name = self::normalizeName($data->name);

        return true;
    }

    /** @throws InvalidPermission */
    private static function normalizeName(string $name): string
    {
        $name = trim(preg_replace('/\s+/', ' ', $name) ?? '');

        if ($name === '') {
            throw new InvalidPermission('O nome da permissão é obrigatório.');
        }

        if (mb_strlen($name) > 255) {
            throw new InvalidPermission(
                'O nome da permissão não pode ultrapassar 255 caracteres.'
            );
        }

        return $name;
    }

    /** @throws InvalidPermission */
    private static function normalizeSlug(string $slug): string
    {
        $slug = mb_strtolower(trim($slug));

        if (preg_match('/^[a-z0-9][a-z0-9._-]*\.[a-z0-9][a-z0-9._-]*$/', $slug) !== 1) {
            throw new InvalidPermission(
                'O identificador da permissão deve seguir o formato recurso.ação.'
            );
        }

        if (mb_strlen($slug) > 255) {
            throw new InvalidPermission(
                'O identificador da permissão não pode ultrapassar 255 caracteres.'
            );
        }

        return $slug;
    }
}
