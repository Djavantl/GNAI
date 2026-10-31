<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Feature\AssistiveTechnologies;

use App\Domains\Inclusion\Application\Queries\AssistiveTechnologies\AssetCodeExistsQuery;
use App\Domains\Inclusion\Domain\DTOs\AssistiveTechnologies\CreateAssistiveTechnologyDTO;
use App\Domains\Inclusion\Domain\Enums\ConservationState;
use App\Domains\Inclusion\Domain\Models\AssistiveTechnology;
use App\Domains\Inclusion\Domain\ValueObjects\AssetCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AssetCodeExistsQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_detects_an_asset_code_used_by_another_technology(): void
    {
        $technology = $this->createTechnology('TA-3001');
        $query = app(AssetCodeExistsQuery::class);

        self::assertTrue($query->execute(AssetCode::from('TA-3001')));
        self::assertFalse($query->execute(
            assetCode: AssetCode::from('TA-3001'),
            ignoreId: $technology->id,
        ));
    }

    private function createTechnology(string $assetCode): AssistiveTechnology
    {
        $technology = AssistiveTechnology::register(new CreateAssistiveTechnologyDTO(
            name: 'Linha Braille',
            isDigital: false,
            isLoanable: true,
            quantity: 1,
            assetCode: AssetCode::from($assetCode),
            conservationState: ConservationState::GOOD,
        ));
        $technology->save();

        return $technology;
    }
}
