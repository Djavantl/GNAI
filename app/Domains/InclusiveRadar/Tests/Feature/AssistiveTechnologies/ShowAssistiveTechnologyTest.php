<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\AssistiveTechnologies;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Deficiency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class ShowAssistiveTechnologyTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_exposes_details_actions_and_safe_rich_text(): void
    {
        $this->withoutVite();
        $user = User::factory()->create(['is_admin' => true]);
        $technology = AssistiveTechnology::factory()->physical()->loanable()->create([
            'notes' => '<p>Descrição <strong>formatada</strong></p><script>alert(1)</script>',
        ]);
        $deficiency = Deficiency::factory()->create(['name' => 'Baixa visão']);
        $technology->deficiencies()->attach($deficiency->id);
        $inspection = Inspection::factory()->for($technology, 'inspectable')->create([
            'description' => '<p>Parecer seguro</p><script>alert(1)</script>',
        ]);

        $this->actingAs($user)->get("/inclusive-radar/assistive-technologies/{$technology->id}")
            ->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('InclusiveRadar/AssistiveTechnologies/Show')
            ->where('assistiveTechnology.id', $technology->id)
            ->where('display.notesHtml', '<p>Descrição <strong>formatada</strong></p>')
            ->where('display.inspectionDetails.0.id', $inspection->id)
            ->where('display.inspectionDetails.0.descriptionHtml', '<p>Parecer seguro</p>')
            ->where('deficiencies.0.name', 'Baixa visão')
            ->where('can.clone', true)
            ->where('can.edit', true)
            ->where('can.inspection', true)
            ->where('can.pdf', true)
            ->where('can.destroy', true)
            );
    }
}
