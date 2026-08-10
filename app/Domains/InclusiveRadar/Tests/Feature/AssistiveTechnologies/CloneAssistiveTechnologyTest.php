<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\AssistiveTechnologies;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\ResourceStatus;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Domains\InclusiveRadar\Domain\Models\InspectionEvidence;
use App\Domains\InclusiveRadar\UI\Controllers\AssistiveTechnologyController;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Deficiency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class CloneAssistiveTechnologyTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/_tests/inclusive-radar/assistive-technologies/{assistiveTechnology}/clone';

    protected function setUp(): void
    {
        parent::setUp();

        Route::get(self::ENDPOINT, [AssistiveTechnologyController::class, 'clone'])
            ->middleware('web');
    }

    public function test_it_opens_creation_with_reusable_data_and_empty_unique_and_inspection_fields(): void
    {
        $user = User::factory()->create();
        $deficiencies = Deficiency::factory()->count(2)->create();
        $technology = AssistiveTechnology::factory()->physical()->create([
            'name' => 'Cadeira de rodas motorizada',
            'notes' => 'Modelo para uso institucional',
            'asset_code' => 'TA-ORIGINAL-01',
            'quantity' => 4,
            'quantity_available' => 2,
            'conservation_state' => ConservationState::GOOD,
            'status' => ResourceStatus::UNAVAILABLE,
            'is_loanable' => true,
            'is_active' => false,
        ]);
        $technology->deficiencies()->attach($deficiencies->modelKeys());
        $inspection = Inspection::factory()->forAssistiveTechnology($technology)->create([
            'description' => 'Cadastro inicial da tecnologia',
        ]);
        InspectionEvidence::factory()->for($inspection)->create([
            'original_name' => 'foto-original-tecnologia.jpg',
        ]);

        $response = $this->actingAs($user)->get(
            str_replace('{assistiveTechnology}', (string) $technology->id, self::ENDPOINT),
        );

        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.assistive-technologies.create');
        $response->assertSee('Clonar Tecnologia Assistiva');
        $response->assertSee('value="Cadeira de rodas motorizada"', false);
        $response->assertSee('Modelo para uso institucional');
        $response->assertSee('value="4"', false);
        $response->assertSee('value="unavailable"', false);

        foreach ($deficiencies as $deficiency) {
            self::assertMatchesRegularExpression(
                '/id="def_'.$deficiency->id.'"[^>]*checked/',
                $response->getContent(),
            );
        }

        self::assertDoesNotMatchRegularExpression(
            '/name="asset_code"[^>]*value="TA-ORIGINAL-01"/',
            $response->getContent(),
        );
        self::assertMatchesRegularExpression('/name="asset_code"[^>]*value=""/', $response->getContent());
        self::assertMatchesRegularExpression('/name="inspection\[date\]"[^>]*value=""/', $response->getContent());
        $response->assertDontSee('Cadastro inicial da tecnologia');
        $response->assertDontSee('foto-original-tecnologia.jpg');
    }
}
