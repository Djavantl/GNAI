<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\Inspections;

use App\Domains\InclusiveRadar\Application\Actions\Inspections\AttachInspectionEvidencesAction;
use App\Domains\InclusiveRadar\Application\Actions\Inspections\CreateInspectionAction;
use App\Domains\InclusiveRadar\Application\Data\Inspections\CreateInspectionData;
use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\InspectionType;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidInspection;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\Auth\Domain\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class CreateInspectionActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_removes_stored_evidences_when_evidence_attachment_fails(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $technology = AssistiveTechnology::factory()
            ->physical()
            ->loanable()
            ->state(['name' => 'Linha Braille'])
            ->create();

        $data = new CreateInspectionData(
            date: now()->toDateString(),
            type: InspectionType::INITIAL,
            evidences: [
                UploadedFile::fake()->image('valid-image.jpg'),
                'invalid-evidence',
            ],
        );

        $inspection = app(CreateInspectionAction::class)->execute(
            inspectable: $technology,
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
        $technology = AssistiveTechnology::factory()
            ->digital()
            ->notLoanable()
            ->state(['name' => 'Slide adaptado'])
            ->create();

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
            inspectable: $technology,
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
