<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Data\Waitlists;

use App\Domains\InclusiveRadar\Domain\Enums\WaitlistStatus;
use Illuminate\Validation\Rules\Enum;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class UpdateWaitlistData extends Data
{
    public function __construct(
        public ?WaitlistStatus $status = null,
        public ?string $observation = null,
    ) {}

    public static function rules(): array
    {
        return [
            'status' => ['nullable', new Enum(WaitlistStatus::class)],
            'observation' => ['nullable', 'string'],
        ];
    }

    public static function messages(): array
    {
        return [
            'status.enum' => 'Status inválido.',
        ];
    }
}
