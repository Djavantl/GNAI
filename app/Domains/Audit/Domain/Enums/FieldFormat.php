<?php

declare(strict_types=1);

namespace App\Domains\Audit\Domain\Enums;

use BackedEnum;
use Carbon\CarbonImmutable;
use DateTimeInterface;

/**
 * Como um campo auditado é gravado e exibido. Formato novo: um case aqui e uma linha em cada `match` abaixo
 * (sem `default` de propósito: o PHP acusa o formato esquecido).
 */
enum FieldFormat: string
{
    case TEXT = 'text';
    /** Texto longo ou HTML de editor; a leitura remove as tags. */
    case RICH_TEXT = 'rich_text';
    case NUMBER = 'number';
    case MONEY = 'money';
    case DATE = 'date';
    case DATE_TIME = 'date_time';
    case TIME = 'time';
    case BOOLEAN = 'boolean';
    /** Enum PHP com `label()`; grava o valor. */
    case ENUM = 'enum';
    /** Chave estrangeira; grava o id, e o nome do registro referenciado vai à parte. */
    case REFERENCE = 'reference';
    /** Conjunto de valores (ex.: permissões, disciplinas); a leitura mostra o que entrou e o que saiu. */
    case LIST = 'list';
    /** Caminho de arquivo enviado; a leitura mostra só o nome. */
    case FILE = 'file';
    /** Senhas e segredos: o valor nunca é gravado, só o fato de ter mudado. */
    case SECRET = 'secret';

    /** O que fica gravado no lugar de um valor sigiloso. */
    public const HIDDEN_VALUE = '[oculto]';

    /**
     * Valor cru (do banco ou de um cast) → valor gravado. Normalizar antes de comparar evita alterações falsas
     * ("10.00" × "10", 1 × true, "2026-01-01 00:00:00" × "2026-01-01").
     */
    public function normalize(mixed $raw): mixed
    {
        // Vazio é ausência: "" × null e [] × null não são mudança.
        if ($raw === null || $raw === [] || (is_string($raw) && trim($raw) === '')) {
            return null;
        }

        return match ($this) {
            self::SECRET => self::HIDDEN_VALUE,
            self::MONEY => number_format((float) $raw, 2, '.', ''),
            self::NUMBER => self::number($raw),
            self::BOOLEAN => filter_var($raw, FILTER_VALIDATE_BOOLEAN),
            self::DATE => self::date($raw)->format('Y-m-d'),
            self::DATE_TIME => self::date($raw)->toIso8601String(),
            self::TIME => substr(self::scalar($raw), 0, 5),
            self::ENUM => $raw instanceof BackedEnum ? $raw->value : self::scalar($raw),
            self::REFERENCE => is_numeric($raw) ? (int) $raw : self::scalar($raw),
            // Ordenada: a ordem em que os itens foram gravados não é mudança.
            self::LIST => collect(is_string($raw) && is_array($list = json_decode($raw, true)) ? $list : (array) $raw)
                ->map(fn (mixed $item) => $item instanceof BackedEnum ? $item->value : (is_numeric($item) ? (int) $item : $item))
                ->sort()
                ->values()
                ->all(),
            self::TEXT, self::RICH_TEXT, self::FILE => self::scalar($raw),
        };
    }

    /**
     * Valor gravado → texto para pessoas. Enum, referência e lista dependem da definição do campo e são
     * tratados em AuditedField::format(); aqui eles saem como foram gravados.
     */
    public function format(mixed $stored): string
    {
        return match ($this) {
            self::MONEY => 'R$ '.number_format((float) $stored, 2, ',', '.'),
            self::NUMBER => is_numeric($stored) ? self::readableNumber($stored) : self::scalar($stored),
            self::BOOLEAN => $stored ? 'Sim' : 'Não',
            self::DATE => CarbonImmutable::parse(self::scalar($stored))->format('d/m/Y'),
            self::DATE_TIME => CarbonImmutable::parse(self::scalar($stored))
                ->setTimezone((string) config('app.timezone'))
                ->format('d/m/Y H:i'),
            self::SECRET => '••••••',
            self::RICH_TEXT => trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags(
                (string) preg_replace('/<(br|\/p|\/li|\/div)[^>]*>/i', ' ', self::scalar($stored)),
            ))) ?? ''),
            self::FILE => basename(self::scalar($stored)),
            self::TEXT, self::TIME, self::ENUM, self::REFERENCE, self::LIST => self::scalar($stored),
        };
    }

    private static function scalar(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : (string) json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    private static function date(mixed $raw): CarbonImmutable
    {
        return $raw instanceof DateTimeInterface
            ? CarbonImmutable::instance($raw)
            : CarbonImmutable::parse(self::scalar($raw));
    }

    /** "10.00" e "10" viram o mesmo 10; "12.5" vira 12.5. */
    private static function number(mixed $raw): int|float|string
    {
        if (! is_numeric($raw)) {
            return self::scalar($raw);
        }

        $number = (float) $raw;

        return floor($number) === $number ? (int) $number : $number;
    }

    /** 1234 → "1.234"; 12.5 → "12,5". */
    private static function readableNumber(mixed $stored): string
    {
        $number = self::number($stored);

        return is_int($number)
            ? number_format($number, 0, ',', '.')
            : rtrim(rtrim(number_format((float) $number, 2, ',', '.'), '0'), ',');
    }
}
