<?php

declare(strict_types=1);

namespace App\Domains\Settings\Domain\ValueObjects;

use App\Domains\Settings\Domain\Enums\BackupRetentionMode;
use Carbon\CarbonInterface;

final readonly class BackupSettings
{
    /**
     * @param  list<string>  $times  Horários no formato H:i, no fuso horário da aplicação.
     */
    public function __construct(
        public BackupRetentionMode $retentionMode,
        public int $retentionValue,
        public array $times,
    ) {}

    /**
     * Padrões que preservam o comportamento anterior: 30 backups mantidos e
     * backup automático diário às 12:05.
     */
    public static function defaults(): self
    {
        return new self(
            retentionMode: BackupRetentionMode::COUNT,
            retentionValue: 30,
            times: ['12:05'],
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        $retentionValue = is_numeric($payload['retention_value'] ?? null)
            ? (int) $payload['retention_value']
            : 0;
        $times = self::normalizeTimes(is_array($payload['times'] ?? null) ? $payload['times'] : []);
        $defaults = self::defaults();

        return new self(
            retentionMode: BackupRetentionMode::tryFrom((string) ($payload['retention_mode'] ?? ''))
                ?? $defaults->retentionMode,
            retentionValue: $retentionValue > 0 ? $retentionValue : $defaults->retentionValue,
            times: $times !== [] ? $times : $defaults->times,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return [
            'retention_mode' => $this->retentionMode->value,
            'retention_value' => $this->retentionValue,
            'times' => self::normalizeTimes($this->times),
        ];
    }

    public function isScheduledAt(CarbonInterface $moment): bool
    {
        return in_array($moment->format('H:i'), $this->times, true);
    }

    /**
     * @param  array<int, mixed>  $times
     * @return list<string>
     */
    private static function normalizeTimes(array $times): array
    {
        $normalized = array_filter(
            array_map(static fn (mixed $time): string => is_string($time) ? trim($time) : '', $times),
            static fn (string $time): bool => preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $time) === 1,
        );
        $normalized = array_values(array_unique($normalized));
        sort($normalized);

        return $normalized;
    }
}
