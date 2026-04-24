<?php

namespace Tests\Unit\InclusiveRadar;

use App\Enums\InclusiveRadar\BarrierStatus;
use App\Enums\InclusiveRadar\ConservationState;
use App\Enums\InclusiveRadar\InspectionType;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Models\InclusiveRadar\Barrier;
use App\Models\InclusiveRadar\Inspection;
use App\Models\User;
use App\Services\InclusiveRadar\InspectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InspectionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected InspectionService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(InspectionService::class);
    }

    public function test_it_creates_an_inspection_with_optimized_images_for_an_item()
    {
        // Arrange
        Storage::fake('public');

        $user = User::factory()->create(['is_admin' => true]);
        $technology = AssistiveTechnology::factory()->physical()->available()->create();

        $this->actingAs($user);

        $data = [
            'state' => ConservationState::GOOD->value,
            'inspection_date' => now()->toDateString(),
            'type' => InspectionType::INITIAL->value,
            'description' => 'Inspeção com imagem',
            'images' => [
                UploadedFile::fake()->image('ta.png'),
            ],
        ];

        // Act
        $inspection = $this->service->createForModel($technology, $data);

        // Assert
        $this->assertInstanceOf(Inspection::class, $inspection);
        $this->assertCount(1, $inspection->images);
        Storage::disk('public')->assertExists($inspection->images->first()->path);
    }

    public function test_it_creates_a_barrier_inspection_with_status()
    {
        // Arrange
        $barrier = Barrier::factory()->create();

        $data = [
            'state' => null,
            'status' => BarrierStatus::IDENTIFIED->value,
            'inspection_date' => now()->toDateString(),
            'type' => InspectionType::INITIAL->value,
            'description' => 'Barreira identificada',
        ];

        // Act
        $inspection = $this->service->createForModel($barrier, $data);

        // Assert
        $this->assertSame(BarrierStatus::IDENTIFIED, $inspection->status);
        $this->assertSame(ConservationState::NOT_APPLICABLE, $inspection->state);
    }

    public function test_it_returns_null_when_update_has_no_relevant_changes_for_auto_inspection()
    {
        // Arrange
        $technology = AssistiveTechnology::factory()->physical()->available()->create();
        $technology = $technology->fresh();

        // Act
        $inspection = $this->service->createInspectionForModel($technology, [
            'inspection_date' => now()->toDateString(),
        ]);

        // Assert
        $this->assertNull($inspection);
    }

    public function test_it_deletes_an_inspection_and_removes_related_files()
    {
        // Arrange
        Storage::fake('public');

        $inspection = Inspection::factory()->create();
        $image = $inspection->images()->create([
            'path' => "inspections/{$inspection->id}/evidencia.webp",
            'original_name' => 'evidencia.png',
            'mime_type' => 'image/webp',
            'size' => 1200,
        ]);

        Storage::disk('public')->put($image->path, 'fake-image');

        // Act
        $this->service->delete($inspection);

        // Assert
        $this->assertDatabaseMissing('inspections', ['id' => $inspection->id]);
        $this->assertDatabaseMissing('inspection_images', ['id' => $image->id]);
        Storage::disk('public')->assertMissing($image->path);
    }
}
