<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Feature\Inspections;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Application\Actions\Inspections\AttachInspectionEvidencesAction;
use App\Domains\Inclusion\Application\Actions\Inspections\CreateInspectionAction;
use App\Domains\Inclusion\Application\Data\Inspections\CreateInspectionData;
use App\Domains\Inclusion\Domain\Enums\ConservationState;
use App\Domains\Inclusion\Domain\Enums\InspectionType;
use App\Domains\Inclusion\Domain\Exceptions\InvalidInspection;
use App\Domains\Inclusion\Domain\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class CreateInspectionActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_inspection_linked_to_the_item(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->physical()->create();

        $inspection = app(CreateInspectionAction::class)->execute(
            inspectable: $item,
            data: new CreateInspectionData(
                date: '2026-07-18',
                type: InspectionType::PERIODIC,
            ),
            registeredBy: $user->id,
            state: ConservationState::REGULAR->value,
            defaultDescription: 'Inspeção registrada na atualização do item.',
        );

        self::assertTrue($inspection->inspectable->is($item));
        $this->assertDatabaseHas('inspections', [
            'id' => $inspection->id,
            'inspectable_type' => 'item',
            'inspectable_id' => $item->id,
            'state' => ConservationState::REGULAR->value,
            'type' => InspectionType::PERIODIC->value,
            'description' => 'Inspeção registrada na atualização do item.',
            'user_id' => $user->id,
        ]);
    }

    public function test_it_removes_stored_evidences_when_evidence_attachment_fails(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $item = Item::factory()->physical()->loanable()->create();

        $data = new CreateInspectionData(
            date: now()->toDateString(),
            type: InspectionType::INITIAL,
            evidences: [
                UploadedFile::fake()->image('valid-image.jpg'),
                'invalid-evidence',
            ],
        );

        $inspection = app(CreateInspectionAction::class)->execute(
            inspectable: $item,
            data: $data,
            registeredBy: $user->id,
            state: ConservationState::GOOD->value,
        );

        try {
            app(AttachInspectionEvidencesAction::class)->execute(
                inspection: $inspection,
                evidences: $data->evidences,
            );

            self::fail('O anexo deveria rejeitar uma evidência inválida.');
        } catch (InvalidInspection) {
            self::assertSame([], Storage::disk('public')->allFiles('inspections'));
            $this->assertDatabaseCount('inspections', 1);
            $this->assertDatabaseCount('inspection_evidences', 0);
        }
    }

    public function test_it_stores_non_image_evidences_without_conversion(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $item = Item::factory()->digital()->create();

        $data = new CreateInspectionData(
            date: now()->toDateString(),
            type: InspectionType::INITIAL,
            evidences: [
                UploadedFile::fake()->create(
                    'slide-adaptado.pptx',
                    128,
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                ),
            ],
        );

        $inspection = app(CreateInspectionAction::class)->execute(
            inspectable: $item,
            data: $data,
            registeredBy: $user->id,
            state: ConservationState::NOT_APPLICABLE->value,
        );

        app(AttachInspectionEvidencesAction::class)->execute(
            inspection: $inspection,
            evidences: $data->evidences,
        );

        $evidence = $inspection->evidences()->firstOrFail();

        self::assertSame('slide-adaptado.pptx', $evidence->original_name);
        self::assertStringEndsWith('.pptx', $evidence->path);
        self::assertFalse($evidence->isImage());
        Storage::disk('public')->assertExists($evidence->path);
    }
}
