<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Feature\AccessibleEducationalMaterials;

use App\Domains\Inclusion\Application\Queries\AccessibleEducationalMaterials\AccessibleEducationalMaterialAssetCodeExistsQuery;
use App\Domains\Inclusion\Domain\DTOs\AccessibleEducationalMaterials\CreateAccessibleEducationalMaterialDTO;
use App\Domains\Inclusion\Domain\Enums\ConservationState;
use App\Domains\Inclusion\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\Inclusion\Domain\ValueObjects\AssetCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AccessibleEducationalMaterialAssetCodeExistsQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_detects_an_asset_code_used_by_another_material(): void
    {
        $material = $this->createMaterial('MPA-3001');
        $query = app(AccessibleEducationalMaterialAssetCodeExistsQuery::class);

        self::assertTrue($query->execute(AssetCode::from('MPA-3001')));
        self::assertFalse($query->execute(
            assetCode: AssetCode::from('MPA-3001'),
            ignoreId: $material->id,
        ));
    }

    private function createMaterial(string $assetCode): AccessibleEducationalMaterial
    {
        $material = AccessibleEducationalMaterial::register(new CreateAccessibleEducationalMaterialDTO(
            name: 'Livro em Braille',
            isDigital: false,
            isLoanable: true,
            quantity: 1,
            assetCode: AssetCode::from($assetCode),
            conservationState: ConservationState::GOOD,
        ));
        $material->save();

        return $material;
    }
}
