<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\Inspections;

use App\Domains\InclusiveRadar\Application\Actions\Inspections\CreateInspectionAction;
use App\Domains\InclusiveRadar\Application\Data\Inspections\CreateInspectionData;
use App\Domains\InclusiveRadar\Domain\DTOs\AssistiveTechnologies\CreateAssistiveTechnologyDTO;
use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\InspectionType;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidInspection;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class CreateInspectionActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_removes_stored_images_when_inspection_creation_fails(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $technology = AssistiveTechnology::register(new CreateAssistiveTechnologyDTO(
            name: 'Linha Braille',
            digital: false,
            loanable: true,
            quantity: 1,
            assetCode: null,
            conservationState: ConservationState::GOOD,
        ));
        $technology->save();

        $data = new CreateInspectionData(
            date: now()->toDateString(),
            type: InspectionType::INITIAL,
            images: [
                UploadedFile::fake()->image('valid-image.jpg'),
                'invalid-image',
            ],
        );

        try {
            app(CreateInspectionAction::class)->execute(
                inspectable: $technology,
                data: $data,
                registeredBy: $user->id,
                state: ConservationState::GOOD->value,
            );

            self::fail('A criação deveria rejeitar uma imagem inválida.');
        } catch (InvalidInspection) {
            self::assertSame([], Storage::disk('public')->allFiles('inspections'));
            $this->assertDatabaseCount('inspections', 0);
            $this->assertDatabaseCount('inspection_images', 0);
        }
    }
}
