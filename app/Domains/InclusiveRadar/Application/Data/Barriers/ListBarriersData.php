<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Data\Barriers;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class ListBarriersData extends Data
{
    public function __construct(
        public ?string $name = null,
        public ?string $category = null,
        public ?string $priority = null,
        public ?string $status = null,
    ) {}
}
