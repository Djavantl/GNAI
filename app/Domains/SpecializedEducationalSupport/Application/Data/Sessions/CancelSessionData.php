<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\Sessions;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class CancelSessionData extends Data
{
    public function __construct(
        public string $cancellationReason,
        public bool $sendNotification = false,
    ) {}

    public static function rules(): array
    {
        return [
            'cancellation_reason' => ['required', 'string', 'min:5'],
            'send_notification' => ['sometimes', 'boolean'],
        ];
    }

    public static function messages(): array
    {
        return [
            'cancellation_reason.required' => 'O motivo do cancelamento é obrigatório.',
            'cancellation_reason.min' => 'O motivo do cancelamento deve ter ao menos 5 caracteres.',
        ];
    }
}
