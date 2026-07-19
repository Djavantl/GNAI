<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\AccessibleEducationalMaterials;

use App\Domains\InclusiveRadar\Domain\DTOs\AccessibleEducationalMaterials\CreateAccessibleEducationalMaterialDTO;
use App\Domains\InclusiveRadar\Domain\DTOs\Inspections\CreateInspectionDTO;
use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\InspectionType;
use App\Domains\InclusiveRadar\Domain\Enums\LoanStatus;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Domains\InclusiveRadar\Domain\Models\Loan;
use App\Domains\InclusiveRadar\UI\Controllers\AccessibleEducationalMaterialController;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class DeleteAccessibleEducationalMaterialTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/_tests/inclusive-radar/accessible-educational-materials';

    protected function setUp(): void
    {
        parent::setUp();

        Route::delete(
            self::ENDPOINT.'/{material}',
            [AccessibleEducationalMaterialController::class, 'destroy'],
        )->middleware('web');
    }

    public function test_it_soft_deletes_a_material_and_preserves_its_history(): void
    {
        $user = User::factory()->create();
        $audience = Deficiency::factory()->create();
        $material = $this->material();
        $material->assignTargetAudience([$audience->id]);

        $inspection = Inspection::register(new CreateInspectionDTO(
            date: now()->toDateString(),
            type: InspectionType::PERIODIC,
            registeredBy: $user->id,
            state: ConservationState::GOOD->value,
        ));
        $inspection->inspectable()->associate($material);
        $inspection->save();

        $response = $this->actingAs($user)->delete(
            self::ENDPOINT.'/'.$material->id,
        );

        $response->assertRedirect(
            route('inclusive-radar.accessible-educational-materials.index'),
        );
        $response->assertSessionHas('success', 'Material removido!');
        $this->assertSoftDeleted('accessible_educational_materials', [
            'id' => $material->id,
        ]);
        $this->assertDatabaseHas('accessible_educational_material_deficiency', [
            'accessible_educational_material_id' => $material->id,
            'deficiency_id' => $audience->id,
        ]);
        $this->assertDatabaseHas('inspections', [
            'id' => $inspection->id,
            'inspectable_id' => $material->id,
            'inspectable_type' => 'accessible_educational_material',
        ]);
    }

    public function test_it_rejects_deletion_while_there_is_an_open_loan(): void
    {
        $user = User::factory()->create();
        $material = $this->material();
        $this->createLoan($material, $user);

        $response = $this->actingAs($user)
            ->from('/accessible-educational-materials')
            ->delete(self::ENDPOINT.'/'.$material->id);

        $response->assertRedirect('/accessible-educational-materials');
        $response->assertSessionHas(
            'error',
            'Não é possível excluir um item com empréstimos ativos.',
        );
        $this->assertDatabaseHas('accessible_educational_materials', [
            'id' => $material->id,
            'deleted_at' => null,
        ]);
    }

    public function test_it_allows_deletion_when_all_loans_were_returned(): void
    {
        $user = User::factory()->create();
        $material = $this->material();
        $this->createLoan(
            material: $material,
            user: $user,
            returnedAt: now(),
        );

        $response = $this->actingAs($user)->delete(
            self::ENDPOINT.'/'.$material->id,
        );

        $response->assertRedirect(
            route('inclusive-radar.accessible-educational-materials.index'),
        );
        $this->assertSoftDeleted('accessible_educational_materials', [
            'id' => $material->id,
        ]);
    }

    private function material(): AccessibleEducationalMaterial
    {
        $material = AccessibleEducationalMaterial::register(new CreateAccessibleEducationalMaterialDTO(
            name: 'Livro em Braille',
            digital: false,
            loanable: true,
            quantity: 1,
            assetCode: null,
            conservationState: ConservationState::GOOD,
        ));
        $material->save();

        return $material;
    }

    private function createLoan(
        AccessibleEducationalMaterial $material,
        User $user,
        mixed $returnedAt = null,
    ): void {
        Loan::query()->create([
            'loanable_id' => $material->id,
            'loanable_type' => $material->getMorphClass(),
            'user_id' => $user->id,
            'loan_date' => now()->subWeek(),
            'due_date' => now()->addWeek(),
            'return_date' => $returnedAt,
            'status' => $returnedAt === null
                ? LoanStatus::ACTIVE
                : LoanStatus::RETURNED,
        ]);
    }
}
