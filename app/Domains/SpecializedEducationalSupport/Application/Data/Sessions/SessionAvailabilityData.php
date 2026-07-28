<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\Sessions;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class SessionAvailabilityData extends Data
{
    /**
     * @param list<int> $studentIds
     */
    public function __construct(
        public array $studentIds = [],
        public ?int $professional = null,
        public ?string $date = null,
    ) {}
}
