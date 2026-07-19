<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\AccessibleEducationalMaterials;

use App\Domains\InclusiveRadar\Application\Queries\AccessibleEducationalMaterials\AccessibleEducationalMaterialAssetCodeExistsQuery;
use App\Domains\InclusiveRadar\Domain\DTOs\AccessibleEducationalMaterials\CreateAccessibleEducationalMaterialDTO;
use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\ValueObjects\AssetCode;
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
            digital: false,
            loanable: true,
            quantity: 1,
            assetCode: AssetCode::from($assetCode),
            conservationState: ConservationState::GOOD,
        ));
        $material->save();

        return $material;
    }
}
