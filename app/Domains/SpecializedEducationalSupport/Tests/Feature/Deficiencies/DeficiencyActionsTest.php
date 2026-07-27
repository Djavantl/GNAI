<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Tests\Feature\Deficiencies;

use App\Domains\SpecializedEducationalSupport\Application\Actions\Deficiencies\CreateDeficiencyAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Deficiencies\DeleteDeficiencyAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Deficiencies\ToggleDeficiencyActiveAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Deficiencies\UpdateDeficiencyAction;
use App\Domains\SpecializedEducationalSupport\Application\Data\Deficiencies\CreateDeficiencyData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Deficiencies\UpdateDeficiencyData;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidDeficiency;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Deficiency;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class DeficiencyActionsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('deficiencies', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('cid_code', 20)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('people', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('document')->nullable();
            $table->date('birth_date');
            $table->string('gender')->default('not_specified');
            $table->string('email')->nullable();
            $table->timestamps();
        });
        Schema::create('students', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->string('registration')->unique();
            $table->date('entry_date')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
        Schema::create('students_deficiencies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('deficiency_id')->constrained('deficiencies')->restrictOnDelete();
            $table->string('severity')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('students_deficiencies');
        Schema::dropIfExists('students');
        Schema::dropIfExists('people');
        Schema::dropIfExists('deficiencies');

        parent::tearDown();
    }

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
        $now = now();
        $personId = DB::table('people')->insertGetId([
            'name' => 'Estudante de teste',
            'document' => '12345678901',
            'birth_date' => '2010-01-01',
            'gender' => 'not_specified',
            'email' => 'estudante@example.test',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $studentId = DB::table('students')->insertGetId([
            'person_id' => $personId,
            'registration' => 'MAT-TESTE',
            'entry_date' => '2026-01-01',
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('students_deficiencies')->insert([
            'student_id' => $studentId,
            'deficiency_id' => $deficiency->getKey(),
            'severity' => 'mild',
            'notes' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
