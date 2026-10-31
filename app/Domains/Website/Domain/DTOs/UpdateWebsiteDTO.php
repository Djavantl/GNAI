<?php

declare(strict_types=1);

namespace App\Domains\Website\Domain\DTOs;

final readonly class UpdateWebsiteDTO
{
    /**
     * @param  list<array<string, mixed>>  $sections
     */
    public function __construct(
        public string $siteName,
        public string $primaryColor,
        public ?string $tagline = null,
        public ?string $logoPath = null,
        public ?string $contactEmail = null,
        public ?string $contactPhone = null,
        public ?string $address = null,
        public ?string $instagramUrl = null,
        public ?string $facebookUrl = null,
        public ?string $youtubeUrl = null,
        public ?string $websiteUrl = null,
        public ?string $footerText = null,
        public array $sections = [],
    ) {}
}
