<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Data\Publications;

use App\Domains\Inclusion\Application\Data\Publications\Concerns\PublicationRules;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class UpdatePublicationData extends Data
{
    use PublicationRules;

    /**
     * @param  list<int|string>  $speakerIds
     * @param  list<array{file?: UploadedFile, alt?: string|null}>  $gallery
     * @param  list<array{id?: int|string, alt?: string|null, remove?: bool|string|null}>  $galleryExisting
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
        public ?string $category = null,
        public ?string $slug = null,
        public ?int $authorId = null,
        public array $speakerIds = [],
        public ?string $linkUrl = null,
        public ?string $linkLabel = null,
        public ?UploadedFile $cover = null,
        public ?string $coverAlt = null,
        public bool $removeCover = false,
        public array $gallery = [],
        public array $galleryExisting = [],
        public ?string $publishedAt = null,
        public bool $isFeatured = false,
        public ?string $seoTitle = null,
        public ?string $seoDescription = null,
    ) {}

    public static function rules(?ValidationContext $context = null): array
    {
        return [
            ...self::publicationRules($context, self::routeEventKey()),
            'gallery_existing' => ['sometimes', 'array'],
            'gallery_existing.*.id' => ['required', 'integer'],
            'gallery_existing.*.alt' => ['nullable', 'string', 'max:255'],
            'gallery_existing.*.remove' => ['sometimes', 'boolean'],
        ];
    }

    private static function routeEventKey(): int|string|null
    {
        $publication = request()->route('publication');

        return $publication instanceof Model ? $publication->getKey() : $publication;
    }

    public static function messages(): array
    {
        return [
            ...self::publicationMessages(),
            'gallery_existing.*.alt.max' => 'O texto alternativo não pode ultrapassar 255 caracteres.',
        ];
    }
}
