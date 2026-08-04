<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Tests\Feature\PedagogicalRecords;

use App\Domains\SpecializedEducationalSupport\Application\Queries\Disciplines\DisciplineHasPedagogicalRecordsQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\PedagogicalDisciplineCategory;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidDiscipline;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Discipline;
use App\Domains\SpecializedEducationalSupport\Domain\Models\PedagogicalRecord;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class PedagogicalRecordDisciplinesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('pedagogical_records', function (Blueprint $table): void {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('disciplines', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('pedagogical_record_disciplines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pedagogical_record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('discipline_id')->constrained()->restrictOnDelete();
            $table->string('category');
            $table->timestamps();
            $table->unique(['pedagogical_record_id', 'discipline_id', 'category']);
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('pedagogical_record_disciplines');
        Schema::dropIfExists('disciplines');
        Schema::dropIfExists('pedagogical_records');

        parent::tearDown();
    }

    public function test_it_syncs_failed_and_at_risk_disciplines_independently(): void
    {
        $recordId = DB::table('pedagogical_records')->insertGetId([
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $disciplineId = DB::table('disciplines')->insertGetId([
            'name' => 'Matemática',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $record = PedagogicalRecord::query()->findOrFail($recordId);

        $record->syncFailedDisciplines([$disciplineId]);
        $record->syncAtRiskDisciplines([$disciplineId]);
        $record->unsetRelation('failedDisciplines');
        $record->unsetRelation('atRiskDisciplines');

        $this->assertSame('Matemática', $record->failed_discipline_names);
        $this->assertSame('Matemática', $record->at_risk_discipline_names);

        $this->assertDatabaseHas('pedagogical_record_disciplines', [
            'pedagogical_record_id' => $recordId,
            'discipline_id' => $disciplineId,
            'category' => PedagogicalDisciplineCategory::FAILED->value,
        ]);
        $this->assertDatabaseHas('pedagogical_record_disciplines', [
            'pedagogical_record_id' => $recordId,
            'discipline_id' => $disciplineId,
            'category' => PedagogicalDisciplineCategory::AT_ACADEMIC_RISK->value,
        ]);

        $record->syncFailedDisciplines([]);

        $this->assertDatabaseMissing('pedagogical_record_disciplines', [
            'pedagogical_record_id' => $recordId,
            'category' => PedagogicalDisciplineCategory::FAILED->value,
        ]);
        $this->assertDatabaseHas('pedagogical_record_disciplines', [
            'pedagogical_record_id' => $recordId,
            'discipline_id' => $disciplineId,
            'category' => PedagogicalDisciplineCategory::AT_ACADEMIC_RISK->value,
        ]);
    }

    public function test_it_prevents_deleting_a_discipline_used_by_a_pedagogical_record(): void
    {
        $recordId = DB::table('pedagogical_records')->insertGetId([
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $disciplineId = DB::table('disciplines')->insertGetId([
            'name' => 'Língua Portuguesa',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('pedagogical_record_disciplines')->insert([
            'pedagogical_record_id' => $recordId,
            'discipline_id' => $disciplineId,
            'category' => PedagogicalDisciplineCategory::AT_ACADEMIC_RISK->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $discipline = Discipline::query()->findOrFail($disciplineId);
        $hasPedagogicalRecords = app(DisciplineHasPedagogicalRecordsQuery::class)->execute($discipline);

        $this->assertTrue($hasPedagogicalRecords);

        try {
            $discipline->ensureCanBeDeleted(
                hasTeachers: false,
                hasCourses: false,
                hasPedagogicalRecords: $hasPedagogicalRecords,
            );
            self::fail('A exclusão deveria ter sido impedida.');
        } catch (InvalidDiscipline $exception) {
            $this->assertSame(
                "Não é possível excluir a disciplina 'Língua Portuguesa' pois ela possui registros em atendimentos pedagógicos.",
                $exception->getMessage(),
            );
        }

        try {
            DB::table('disciplines')->where('id', $disciplineId)->delete();
            self::fail('A restrição do banco deveria ter impedido a exclusão.');
        } catch (QueryException) {
            $this->addToAssertionCount(1);
        }

        $this->assertDatabaseHas('disciplines', ['id' => $disciplineId]);
    }
}
