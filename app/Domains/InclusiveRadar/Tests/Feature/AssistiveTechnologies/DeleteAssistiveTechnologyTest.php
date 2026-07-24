<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\AssistiveTechnologies;

use App\Domains\InclusiveRadar\Domain\DTOs\AssistiveTechnologies\CreateAssistiveTechnologyDTO;
use App\Domains\InclusiveRadar\Domain\DTOs\Inspections\CreateInspectionDTO;
use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\InspectionType;
use App\Domains\InclusiveRadar\Domain\Enums\LoanStatus;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Domains\InclusiveRadar\Domain\Models\Loan;
use App\Domains\InclusiveRadar\UI\Controllers\AssistiveTechnologyController;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Domains\Auth\Domain\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class DeleteAssistiveTechnologyTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/_tests/inclusive-radar/assistive-technologies';

    protected function setUp(): void
    {
        parent::setUp();

        Route::delete(
            self::ENDPOINT.'/{assistiveTechnology}',
            [AssistiveTechnologyController::class, 'destroy'],
        )->middleware('web');
    }

    public function test_it_soft_deletes_a_technology_and_preserves_its_history(): void
    {
        $user = User::factory()->create();
        $audience = Deficiency::factory()->create();
        $technology = $this->technology();
        $technology->assignTargetAudience([$audience->id]);

        $inspection = Inspection::register(new CreateInspectionDTO(
            date: now()->toDateString(),
            type: InspectionType::PERIODIC,
            registeredBy: $user->id,
            state: ConservationState::GOOD->value,
        ));
        $inspection->inspectable()->associate($technology);
        $inspection->save();

        $response = $this->actingAs($user)->delete(
            self::ENDPOINT.'/'.$technology->id,
        );

        $response->assertRedirect(
            route('inclusive-radar.assistive-technologies.index'),
        );
        $response->assertSessionHas(
            'success',
            'Tecnologia removida com sucesso!',
        );
        $this->assertSoftDeleted('assistive_technologies', [
            'id' => $technology->id,
        ]);
        $this->assertDatabaseHas('assistive_technology_deficiency', [
            'assistive_technology_id' => $technology->id,
            'deficiency_id' => $audience->id,
        ]);
        $this->assertDatabaseHas('inspections', [
            'id' => $inspection->id,
            'inspectable_id' => $technology->id,
            'inspectable_type' => 'assistive_technology',
        ]);
    }

    public function test_it_rejects_deletion_while_there_is_an_open_loan(): void
    {
        $user = User::factory()->create();
        $technology = $this->technology();
        $this->createLoan($technology, $user);

        $response = $this->actingAs($user)
            ->from('/assistive-technologies')
            ->delete(self::ENDPOINT.'/'.$technology->id);

        $response->assertRedirect('/assistive-technologies');
        $response->assertSessionHas(
            'error',
            'Não é possível excluir um item com empréstimos ativos.',
        );
        $this->assertDatabaseHas('assistive_technologies', [
            'id' => $technology->id,
            'deleted_at' => null,
        ]);
    }

    public function test_it_allows_deletion_when_all_loans_were_returned(): void
    {
        $user = User::factory()->create();
        $technology = $this->technology();
        $this->createLoan(
            technology: $technology,
            user: $user,
            returnedAt: now(),
        );

        $response = $this->actingAs($user)->delete(
            self::ENDPOINT.'/'.$technology->id,
        );

        $response->assertRedirect(
            route('inclusive-radar.assistive-technologies.index'),
        );
        $this->assertSoftDeleted('assistive_technologies', [
            'id' => $technology->id,
        ]);
    }

    private function technology(): AssistiveTechnology
    {
        $technology = AssistiveTechnology::register(new CreateAssistiveTechnologyDTO(
            name: 'Linha Braille',
            isDigital: false,
            isLoanable: true,
            quantity: 1,
            assetCode: null,
            conservationState: ConservationState::GOOD,
        ));
        $technology->save();

        return $technology;
    }

    private function createLoan(
        AssistiveTechnology $technology,
        User $user,
        mixed $returnedAt = null,
    ): void {
        Loan::query()->create([
            'loanable_id' => $technology->id,
            'loanable_type' => $technology->getMorphClass(),
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
