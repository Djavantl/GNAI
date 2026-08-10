<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\Models;

use App\Domains\InclusiveRadar\Domain\DTOs\InstitutionalEvents\CreateInstitutionalEventDTO;
use App\Domains\InclusiveRadar\Domain\DTOs\InstitutionalEvents\UpdateInstitutionalEventDTO;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidInstitutionalEvent;
use Carbon\Carbon;
use Database\Factories\Domains\InclusiveRadar\InstitutionalEventFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[UseFactory(InstitutionalEventFactory::class)]
class InstitutionalEvent extends Model
{
    use HasFactory;

    protected $table = 'institutional_events';

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'location',
        'organizer',
        'audience',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean',
    ];

    /**
     * @throws InvalidInstitutionalEvent
     */
    public static function schedule(CreateInstitutionalEventDTO $data): self
    {
        return new self(self::attributesFrom($data));
    }

    /**
     * @throws InvalidInstitutionalEvent
     */
    public function revise(UpdateInstitutionalEventDTO $data): void
    {
        $this->fill(self::attributesFrom($data));
    }

    /**
     * @throws InvalidInstitutionalEvent
     */
    private static function attributesFrom(CreateInstitutionalEventDTO|UpdateInstitutionalEventDTO $data): array
    {
        self::ensureValidPeriod(
            startDate: $data->startDate,
            endDate: $data->endDate,
            startTime: $data->startTime,
            endTime: $data->endTime,
        );

        return [
            'title' => self::normalizeText($data->title),
            'description' => self::normalizeNullableText($data->description),
            'start_date' => Carbon::parse($data->startDate)->toDateString(),
            'end_date' => Carbon::parse($data->endDate)->toDateString(),
            'start_time' => Carbon::createFromFormat('H:i', self::normalizeTime($data->startTime))->format('H:i'),
            'end_time' => Carbon::createFromFormat('H:i', self::normalizeTime($data->endTime))->format('H:i'),
            'location' => self::normalizeText($data->location),
            'organizer' => self::normalizeNullableText($data->organizer),
            'audience' => self::normalizeNullableText($data->audience),
            'is_active' => $data->isActive,
        ];
    }

    /**
     * @throws InvalidInstitutionalEvent
     */
    private static function ensureValidPeriod(string $startDate, string $endDate, string $startTime, string $endTime): void
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $startsAt = Carbon::createFromFormat('H:i', self::normalizeTime($startTime));
        $endsAt = Carbon::createFromFormat('H:i', self::normalizeTime($endTime));

        if ($end->lt($start)) {
            throw new InvalidInstitutionalEvent('A data de término não pode ser anterior à data de início.');
        }

        if ($start->eq($end) && $endsAt->lte($startsAt)) {
            throw new InvalidInstitutionalEvent('O horário de término deve ser maior que o horário de início para o mesmo dia.');
        }
    }

    private static function normalizeText(string $value): string
    {
        return trim($value);
    }

    private static function normalizeNullableText(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private static function normalizeTime(string $time): string
    {
        return substr($time, 0, 5);
    }
}
