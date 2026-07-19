<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Domain\ValueObjects;

use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidAssetCode;
use App\Domains\InclusiveRadar\Domain\ValueObjects\AssetCode;
use PHPUnit\Framework\TestCase;

final class AssetCodeTest extends TestCase
{
    public function test_it_creates_an_asset_code(): void
    {
        $assetCode = AssetCode::from('TA-1001');

        self::assertSame('TA-1001', $assetCode->value());
        self::assertSame('TA-1001', (string) $assetCode);
    }

    public function test_it_trims_surrounding_whitespace(): void
    {
        $assetCode = AssetCode::from('  TA-1001  ');

        self::assertSame('TA-1001', $assetCode->value());
    }

    public function test_optional_returns_null_for_absent_value(): void
    {
        self::assertNull(AssetCode::optional(null));
        self::assertNull(AssetCode::optional(''));
        self::assertNull(AssetCode::optional('   '));
    }

    public function test_optional_creates_asset_code_when_value_is_present(): void
    {
        $assetCode = AssetCode::optional('MPA-2001');

        self::assertInstanceOf(AssetCode::class, $assetCode);
        self::assertSame('MPA-2001', $assetCode->value());
    }

    public function test_it_rejects_empty_asset_code(): void
    {
        $this->expectException(InvalidAssetCode::class);
        $this->expectExceptionMessage(
            'O código patrimonial não pode ser vazio.'
        );

        AssetCode::from('   ');
    }

    public function test_it_accepts_asset_code_with_exactly_fifty_characters(): void
    {
        $value = str_repeat('A', 50);

        self::assertSame($value, AssetCode::from($value)->value());
    }

    public function test_it_rejects_asset_code_longer_than_fifty_characters(): void
    {
        $this->expectException(InvalidAssetCode::class);
        $this->expectExceptionMessage(
            'O código patrimonial deve possuir no máximo 50 caracteres.'
        );

        AssetCode::from(str_repeat('A', 51));
    }

    public function test_it_compares_asset_codes_by_value(): void
    {
        $first = AssetCode::from('TA-1001');
        $same = AssetCode::from('TA-1001');
        $different = AssetCode::from('TA-1002');

        self::assertTrue($first->equals($same));
        self::assertFalse($first->equals($different));
    }
}
