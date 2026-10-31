<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Feature\Deficiencies;

use App\Domains\Inclusion\Application\Actions\Deficiencies\CreateDeficiencyAction;
use App\Domains\Inclusion\Application\Actions\Deficiencies\DeleteDeficiencyAction;
use App\Domains\Inclusion\Application\Actions\Deficiencies\ToggleDeficiencyActiveAction;
use App\Domains\Inclusion\Application\Actions\Deficiencies\UpdateDeficiencyAction;
use App\Domains\Inclusion\Application\Data\Deficiencies\CreateDeficiencyData;
use App\Domains\Inclusion\Application\Data\Deficiencies\UpdateDeficiencyData;
use App\Domains\Inclusion\Domain\Exceptions\InvalidDeficiency;
use App\Domains\Inclusion\Domain\Models\Deficiency;
use App\Domains\Inclusion\Domain\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DeficiencyActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_and_normalizes_a_deficiency(): void
    {
        $deficiency = app(CreateDeficiencyAction::class)->execute(
            new CreateDeficiencyData(
                name: '  Deficiência visual  ',
                cidCode: ' h54.0 ',
                description: '  Baixa visão.  ',
            ),
        );

        self::assertSame('Deficiência visual', $deficiency->name);
        self::assertSame('H54.0', $deficiency->cid_code);
        self::assertSame('Baixa visão.', $deficiency->description);
        self::assertTrue($deficiency->is_active);
    }

    public function test_it_updates_data_without_changing_the_active_status(): void
    {
        $deficiency = Deficiency::factory()->inactive()->create();

        $updated = app(UpdateDeficiencyAction::class)->execute(
            deficiency: $deficiency,
            data: new UpdateDeficiencyData(
                name: '  Transtorno do espectro autista  ',
                cidCode: ' f84.0 ',
                description: '  ',
            ),
        );

        self::assertSame('Transtorno do espectro autista', $updated->name);
        self::assertSame('F84.0', $updated->cid_code);
        self::assertNull($updated->description);
        self::assertFalse($updated->is_active);
    }

    public function test_it_toggles_the_active_status_when_there_are_no_students(): void
    {
        $deficiency = Deficiency::factory()->active()->create();
        $action = app(ToggleDeficiencyActiveAction::class);

        $deactivated = $action->execute($deficiency);
        self::assertFalse($deactivated->is_active);

        $activated = $action->execute($deactivated);
        self::assertTrue($activated->is_active);
    }

    public function test_it_rejects_deactivation_when_there_is_a_linked_student(): void
    {
        $deficiency = Deficiency::factory()->active()->create();
        $this->linkStudentTo($deficiency);

        $this->expectException(InvalidDeficiency::class);
        $this->expectExceptionMessage(
            'Este perfil está vinculado a um ou mais alunos e não pode ser desativado.'
        );

        app(ToggleDeficiencyActiveAction::class)->execute($deficiency);
    }

    public function test_it_deletes_a_deficiency_without_students(): void
    {
        $deficiency = Deficiency::factory()->create();

        app(DeleteDeficiencyAction::class)->execute($deficiency);

        $this->assertDatabaseMissing('deficiencies', [
            'id' => $deficiency->id,
        ]);
    }

    public function test_it_rejects_deletion_when_there_is_a_linked_student(): void
    {
        $deficiency = Deficiency::factory()->create();
        $this->linkStudentTo($deficiency);

        $this->expectException(InvalidDeficiency::class);
        $this->expectExceptionMessage(
            'Este perfil está vinculado a um ou mais alunos e não pode ser desativado.'
        );

        app(DeleteDeficiencyAction::class)->execute($deficiency);
    }

    private function linkStudentTo(Deficiency $deficiency): void
    {
        $deficiency->students()->attach(Student::factory()->active()->create(), [
            'severity' => 'mild',
        ]);
    }
}
