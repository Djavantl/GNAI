<?php

declare(strict_types=1);

namespace App\Domains\Audit\Application\Data;

use App\Domains\Audit\Domain\Enums\AuditEvent;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/** Filtros da tela de auditoria. Todos opcionais; vazio é "sem filtro". */
#[MapInputName(SnakeCaseMapper::class)]
final class ListAuditRecordsData extends Data
{
    /** Valor do filtro de autor para as operações sem usuário (comandos, rotinas). */
    public const SYSTEM_ACTOR = 'system';

    public function __construct(
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
        /** id do usuário ou `system` */
        public ?string $actor = null,
        public ?string $type = null,
        public ?string $event = null,
        /** texto na identificação do registro ou no nome do autor */
        public ?string $search = null,
        public int $perPage = 25,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        foreach ($properties as $key => $value) {
            if (is_string($value)) {
                $properties[$key] = trim($value) === '' ? null : trim($value);
            }
        }

        return $properties;
    }

    public static function rules(): array
    {
        return [
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
            // O id de um usuário ou o "system"; qualquer outra coisa é recusada, não ignorada.
            'actor' => ['nullable', 'string', 'regex:/^('.self::SYSTEM_ACTOR.'|[0-9]+)$/'],
            'type' => ['nullable', 'string', 'max:60'],
            'event' => ['nullable', Rule::enum(AuditEvent::class)],
            'search' => ['nullable', 'string', 'max:100'],
            'per_page' => ['integer', 'min:1', 'max:100'],
        ];
    }

    /** @return array<string, string> filtros no formato da tela, com vazio no lugar de nulo */
    public function toFilters(): array
    {
        return [
            'date_from' => $this->dateFrom ?? '',
            'date_to' => $this->dateTo ?? '',
            'actor' => $this->actor ?? '',
            'type' => $this->type ?? '',
            'event' => $this->event ?? '',
            'search' => $this->search ?? '',
        ];
    }
}
