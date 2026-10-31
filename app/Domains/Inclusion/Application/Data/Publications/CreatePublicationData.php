<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Data\Publications;

use App\Domains\Inclusion\Application\Data\Publications\Concerns\PublicationRules;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class CreatePublicationData extends Data
{
    use PublicationRules;

    /**
     * @param  list<array{id?: int|string, participation?: string|null}>  $speakers
     * @param  list<int|string>  $tagIds
     * @param  list<array{file?: UploadedFile, alt?: string|null}>  $gallery
     */
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
        public bool $isActive = false,
        public bool $hasSchedule = true,
        public string $type = 'event',
        public ?string $summary = null,
        public ?string $slug = null,
        public ?int $authorId = null,
        public array $speakers = [],
        public array $tagIds = [],
        public ?string $linkUrl = null,
        public ?string $linkLabel = null,
        public ?UploadedFile $cover = null,
        public ?string $coverAlt = null,
        public bool $removeCover = false,
        public array $gallery = [],
        public ?string $publishedAt = null,
        public bool $isFeatured = false,
        public ?string $seoTitle = null,
        public ?string $seoDescription = null,
    ) {}

    public static function rules(?ValidationContext $context = null): array
    {
        return [
            ...self::publicationRules($context, null),

        ];
    }

    public static function messages(): array
    {
        return [
            ...self::publicationMessages(),
        ];
    }
}
