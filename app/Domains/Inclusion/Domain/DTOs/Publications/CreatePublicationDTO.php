<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\DTOs\Publications;

final readonly class CreatePublicationDTO
{
    public function __construct(
        public string $title,
        public ?string $startDate = null,
        public ?string $endDate = null,
        public ?string $startTime = null,
        public ?string $endTime = null,
        public ?string $location = null,
        public ?string $description = null,
        public ?string $organizer = null,
        public ?string $audience = null,
        public bool $isActive = true,
        public bool $hasSchedule = true,
        public string $type = 'event',
        public ?string $summary = null,
        public ?string $slug = null,
        public ?int $authorId = null,
        public ?string $linkUrl = null,
        public ?string $linkLabel = null,
        public ?string $coverAlt = null,
        public ?string $publishedAt = null,
        public bool $isFeatured = false,
        public ?string $seoTitle = null,
        public ?string $seoDescription = null,
    ) {}
}
