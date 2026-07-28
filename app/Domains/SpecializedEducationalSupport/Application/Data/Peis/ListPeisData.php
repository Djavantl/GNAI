<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\Peis;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class ListPeisData extends Data
{
    public function __construct(
        public ?int $studentId = null,
        public ?int $semesterId = null,
        public null|bool|string|int $isFinished = null,
        public ?int $version = null,
    ) {}
}
