<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\AeeRecords;

use App\Domains\SpecializedEducationalSupport\Application\Data\AeeRecords\Concerns\HasAeeRecordValidation;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class UpdateAeeRecordData extends Data
{
    use HasAeeRecordValidation;

    public function __construct(
        public string $duration,
        public string $activitiesPerformed,
        public ?string $strategiesUsed,
        public ?string $resourcesUsed,
        public ?string $generalObservations,
        public array $evaluations,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        return self::prepareEvaluations($properties);
    }

    public static function rules(): array
    {
        return self::contentRules();
    }
}
