<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain\DTOs\Permissions;

final readonly class PermissionDTO
{
    public function __construct(
        public string $name,
        public string $slug,
    ) {}

    /**
     * @return array{name: string, slug: string}
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
        ];
    }

    /**
     * @param array{name: string, slug: string} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            slug: $data['slug'],
        );
    }
}
