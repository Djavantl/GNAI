<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Unit\Domain\ValueObjects;

use App\Domains\Inclusion\Domain\Exceptions\InvalidAssetCode;
use App\Domains\Inclusion\Domain\ValueObjects\AssetCode;
use PHPUnit\Framework\TestCase;

final class AssetCodeTest extends TestCase
{
    public function test_it_trims_surrounding_whitespace(): void
    {
        $assetCode = AssetCode::from('  TA-1001  ');

        self::assertSame('TA-1001', $assetCode->value());
    }

    public function test_it_treats_a_blank_optional_value_as_absent(): void
    {
        self::assertNull(AssetCode::optional(null));
        self::assertNull(AssetCode::optional(''));
        self::assertNull(AssetCode::optional('   '));
    }

    public function test_it_rejects_empty_asset_code(): void
    {
        $this->expectException(InvalidAssetCode::class);
        $this->expectExceptionMessage(
            'O código patrimonial não pode ser vazio.'
        );

        AssetCode::from('   ');
    }

    public function test_it_rejects_asset_code_longer_than_fifty_characters(): void
    {
        $this->expectException(InvalidAssetCode::class);
        $this->expectExceptionMessage(
            'O código patrimonial deve possuir no máximo 50 caracteres.'
        );

        AssetCode::from(str_repeat('A', 51));
    }
}
