<?php

declare(strict_types=1);

namespace App\Domains\Reporting\Tests\Feature;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Backup\Domain\Enums\BackupStatus;
use App\Domains\Backup\Domain\Models\Backup;
use App\Domains\Inclusion\Domain\Enums\StudentStatus;
use App\Domains\Inclusion\Domain\Models\Barrier;
use App\Domains\Inclusion\Domain\Models\Deficiency;
use App\Domains\Inclusion\Domain\Models\Inspection;
use App\Domains\Inclusion\Domain\Models\Item;
use App\Domains\Inclusion\Domain\Models\Loan;
use App\Domains\Inclusion\Domain\Models\Student;
use App\Domains\Inclusion\Domain\Models\Waitlist;
use App\Domains\Reporting\Application\Services\ReportCatalog;
use App\Domains\Reporting\Application\Services\ReportRelationCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_primary_sources_and_hides_relation_only_sources(): void
    {
        $response = $this->actingAs($this->admin())->getJson(route('reports.sources'));

        $response->assertOk()
            ->assertJsonFragment(['key' => 'specialized-support.students', 'label' => 'Alunos'])
            ->assertJsonFragment(['key' => 'inclusive-radar.items'])
            ->assertJsonFragment(['key' => 'inclusive-radar.loans'])
            ->assertJsonFragment(['key' => 'inclusive-radar.inspections'])
            ->assertJsonMissing(['key' => 'specialized-support.student-deficiencies']);
    }

    public function test_it_describes_and_runs_every_listed_source(): void
    {
        $admin = $this->admin();
        $sources = $this->actingAs($admin)->getJson(route('reports.sources'))->assertOk()->json();

        foreach ($sources as $source) {
            $metadata = $this->actingAs($admin)
                ->getJson(route('reports.metadata', ['source' => $source['key']]))
                ->assertOk()
                ->json();

            $this->actingAs($admin)
                ->postJson(route('reports.run'), [
                    'source' => $source['key'],
                    'columns' => [array_key_first($metadata['columns'])],
                    'limit' => 1,
                ])
                ->assertOk()
                ->assertJsonStructure(['rows', 'headers', 'total']);
        }
    }

    public function test_it_only_exposes_explicitly_declared_columns_and_filters(): void
    {
        $response = $this->actingAs($this->admin())->getJson(route('reports.metadata', [
            'source' => 'specialized-support.students',
        ]));

        $response->assertOk()
            ->assertJsonPath('columns.registration', 'Matrícula')
            ->assertJsonMissing(['key' => 'password'])
            ->assertJsonFragment(['key' => 'status', 'type' => 'select']);
    }

    public function test_it_exposes_items_and_barriers_as_inspection_targets(): void
    {
        $metadata = $this->metadata('inclusive-radar.inspections');

        $relations = collect($metadata['relations'])->keyBy('name');

        self::assertArrayHasKey('asset_code', $relations['item']['columns']);
        self::assertArrayNotHasKey('name', $relations['item']['columns']);
        self::assertArrayHasKey('priority', $relations['barrier']['columns']);
        self::assertSame(
            ['original_name' => 'Nome do arquivo', 'mime_type' => 'Formato', 'size' => 'Tamanho (bytes)'],
            $relations['evidences']['columns'],
        );
    }

    public function test_it_does_not_repeat_the_inspected_resource_in_its_inspections(): void
    {
        foreach (['inclusive-radar.items', 'inclusive-radar.barriers'] as $source) {
            $inspections = collect($this->metadata($source)['relations'])->firstWhere('name', 'inspections');

            self::assertArrayNotHasKey('inspectable_type', $inspections['columns'], $source);
            self::assertArrayNotHasKey('inspectable', $inspections['columns'], $source);
            self::assertArrayHasKey('inspection_date', $inspections['columns'], $source);
        }
    }

    public function test_it_uses_history_relations_for_students_instead_of_current_shortcuts(): void
    {
        $metadata = $this->metadata('specialized-support.students');

        $relations = collect($metadata['relations'])->keyBy('name');

        self::assertFalse($relations->has('person'));
        self::assertFalse($relations->has('currentContext'));
        self::assertFalse($relations->has('currentCourse'));
        self::assertContains('is_current', array_column($relations['contexts']['column_definitions'], 'key'));
        self::assertContains('is_current', array_column($relations['studentCourses']['column_definitions'], 'key'));
    }

    /**
     * @param  list<string>  $repeated
     */
    #[DataProvider('relationsBackToTheParent')]
    public function test_it_does_not_repeat_the_parent_identity_in_its_relations(string $source, string $relation, array $repeated, string $kept): void
    {
        $metadata = $this->metadata($source);

        $columns = collect($metadata['relations'])->firstWhere('name', $relation)['columns'];

        foreach ($repeated as $column) {
            self::assertArrayNotHasKey($column, $columns, "{$source}.{$relation}.{$column}");
        }
        self::assertArrayHasKey($kept, $columns);
    }

    /**
     * @return array<string, array{string, string, list<string>, string}>
     */
    public static function relationsBackToTheParent(): array
    {
        return [
            'student contexts' => ['specialized-support.students', 'contexts', ['student', 'registration'], 'is_current'],
            'student courses' => ['specialized-support.students', 'studentCourses', ['student', 'registration'], 'is_current'],
            'student loans' => ['specialized-support.students', 'loans', ['student'], 'loan_date'],
            'student waitlists' => ['specialized-support.students', 'waitlists', ['student'], 'requested_at'],
            'professional loans' => ['specialized-support.professionals', 'loans', ['professional'], 'loan_date'],
            'professional waitlists' => ['specialized-support.professionals', 'waitlists', ['professional'], 'requested_at'],
            'professional user' => ['specialized-support.professionals', 'user', ['name', 'email'], 'role'],
            'user professional' => ['auth.users', 'professional', ['name', 'email'], 'registration'],
            'user loans' => ['auth.users', 'loans', ['registered_by'], 'loan_date'],
            'user waitlists' => ['auth.users', 'waitlists', ['registered_by'], 'requested_at'],
            'guardian student' => ['specialized-support.guardians', 'student', ['name'], 'registration'],
            'guardian person' => ['specialized-support.guardians', 'person', ['name', 'document'], 'birth_date'],
            'person guardians' => ['specialized-support.people', 'guardians', ['name', 'document'], 'relationship'],
            'context peis' => ['specialized-support.student-contexts', 'peis', ['student', 'registration', 'semester'], 'version'],
            'pei context' => ['specialized-support.peis', 'studentContext', ['student', 'registration', 'semester'], 'version'],
            'document semester' => ['specialized-support.student-documents', 'semester', ['label', 'year', 'term'], 'start_date'],
            'context semester' => ['specialized-support.student-contexts', 'semester', ['label', 'year', 'term'], 'start_date'],
            'pei semester' => ['specialized-support.peis', 'semester', ['label', 'year', 'term'], 'start_date'],
        ];
    }

    public function test_it_never_repeats_a_field_already_flattened_by_the_parent(): void
    {
        $sources = $this->app->make(ReportCatalog::class);
        $relations = $this->app->make(ReportRelationCatalog::class);

        foreach ($sources->all() as $source) {
            foreach ($relations->for($source) as $relationName => $relation) {
                $prefix = $relationName.'.';
                $flattenedPaths = collect($source->fieldPaths())
                    ->filter(static fn (string $path): bool => str_starts_with($path, $prefix))
                    ->map(static fn (string $path): string => substr($path, strlen($prefix)))
                    ->all();
                $relatedPaths = $relation['source']->fieldPaths();

                foreach (array_keys($relation['columns']) as $column) {
                    self::assertNotContains(
                        $relatedPaths[$column] ?? $column,
                        $flattenedPaths,
                        "A relação {$source->key()}.{$relationName} repete a coluna {$column}.",
                    );
                }
            }
        }
    }

    public function test_it_never_exposes_two_relations_to_the_same_model(): void
    {
        $sources = $this->app->make(ReportCatalog::class);
        $relations = $this->app->make(ReportRelationCatalog::class);

        foreach ($sources->all() as $source) {
            $targets = collect($relations->for($source))
                ->map(static fn (array $relation): string => $relation['source']->modelClass());

            self::assertSame(
                $targets->unique()->count(),
                $targets->count(),
                "A fonte {$source->key()} expõe mais de uma relação para o mesmo model.",
            );
        }
    }

    public function test_it_filters_by_an_allowed_field_and_formats_enum_values(): void
    {
        Student::factory()->create(['registration' => 'MAT000001', 'status' => StudentStatus::ACTIVE]);
        Student::factory()->create(['registration' => 'MAT000002', 'status' => StudentStatus::DROPPED]);

        $response = $this->actingAs($this->admin())->postJson(route('reports.run'), [
            'source' => 'specialized-support.students',
            'columns' => ['registration', 'status'],
            'filters' => [['field' => 'status', 'operator' => 'eq', 'value' => 'active']],
        ]);

        $response->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('rows.0.registration', 'MAT000001')
            ->assertJsonPath('rows.0.status', 'Ativo');
    }

    public function test_it_selects_and_filters_many_to_many_fields_including_pivot_data(): void
    {
        $included = Student::factory()->create(['registration' => 'MAT-REL-1']);
        $excluded = Student::factory()->create(['registration' => 'MAT-REL-2']);
        $visual = Deficiency::factory()->create(['name' => 'Deficiência visual', 'cid_code' => 'H54']);
        $hearing = Deficiency::factory()->create(['name' => 'Deficiência auditiva', 'cid_code' => 'H90']);
        $included->deficiencies()->attach($visual, ['severity' => 'moderate', 'notes' => 'Acompanhamento']);
        $included->deficiencies()->attach($hearing, ['severity' => 'mild']);
        $excluded->deficiencies()->attach($hearing, ['severity' => 'severe']);

        $response = $this->actingAs($this->admin())->postJson(route('reports.run'), [
            'source' => 'specialized-support.students',
            'columns' => ['registration', 'deficiencies.name', 'deficiencies.cid_code', 'deficiencies.pivot.severity'],
            'filters' => [['field' => 'deficiencies.name', 'operator' => 'contains', 'value' => 'visual']],
        ]);

        $response->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('rows.0.registration', 'MAT-REL-1')
            ->assertJsonPath('rows.0.deficiencies__name', 'Deficiência visual, Deficiência auditiva')
            ->assertJsonPath('rows.0.deficiencies__cid_code', 'H54, H90')
            ->assertJsonPath('rows.0.deficiencies__pivot__severity', 'moderate, mild');
    }

    public function test_it_limits_related_values_to_the_matching_filters_when_requested(): void
    {
        $admin = $this->admin();
        $student = Student::factory()->create(['registration' => 'MAT-MODOS']);
        $student->deficiencies()->attach(Deficiency::factory()->create(['name' => 'Visual']), ['severity' => 'moderate']);
        $student->deficiencies()->attach(Deficiency::factory()->create(['name' => 'Auditiva']), ['severity' => 'mild']);

        $byRelatedField = $this->actingAs($admin)->postJson(route('reports.run'), [
            'source' => 'specialized-support.students',
            'columns' => ['registration', 'deficiencies.name'],
            'filters' => [['field' => 'deficiencies.name', 'operator' => 'eq', 'value' => 'Visual']],
            'filter_related_values' => true,
        ]);
        $byPivotField = $this->actingAs($admin)->postJson(route('reports.run'), [
            'source' => 'specialized-support.students',
            'columns' => ['registration', 'deficiencies.name'],
            'filters' => [['field' => 'deficiencies.pivot.severity', 'operator' => 'eq', 'value' => 'moderate']],
            'filter_related_values' => true,
        ]);

        $byRelatedField->assertOk()->assertJsonPath('rows.0.deficiencies__name', 'Visual');
        $byPivotField->assertOk()->assertJsonPath('rows.0.deficiencies__name', 'Visual');
    }

    public function test_it_resolves_a_singular_relation_as_a_display_name_and_nested_columns(): void
    {
        $responsible = User::factory()->create(['name' => 'Responsável pelo backup', 'email' => 'backup@example.test']);
        Backup::query()->create([
            'file_name' => 'manual.zip',
            'file_path' => 'backups/manual.zip',
            'size' => '1 MB',
            'status' => BackupStatus::SUCCESS,
            'user_id' => $responsible->id,
        ]);

        $response = $this->actingAs($this->admin())->postJson(route('reports.run'), [
            'source' => 'backup.backups',
            'columns' => ['file_name', 'user', 'user.email'],
        ]);

        $response->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('rows.0.user', 'Responsável pelo backup')
            ->assertJsonPath('rows.0.user__email', 'backup@example.test');
    }

    public function test_it_rejects_a_column_not_declared_by_the_source(): void
    {
        $response = $this->actingAs($this->admin())->postJson(route('reports.run'), [
            'source' => 'specialized-support.students',
            'columns' => ['person_id'],
        ]);

        $response->assertUnprocessable()
            ->assertJsonPath('message', 'A coluna person_id não está disponível neste relatório.');
    }

    public function test_it_rejects_an_operator_not_declared_by_the_filter(): void
    {
        $response = $this->actingAs($this->admin())->postJson(route('reports.run'), [
            'source' => 'specialized-support.students',
            'columns' => ['registration'],
            'filters' => [['field' => 'status', 'operator' => 'contains', 'value' => 'active']],
        ]);

        $response->assertUnprocessable()
            ->assertJsonPath('message', 'O operador selecionado não é permitido para esse filtro.');
    }

    /**
     * @param  class-string<Loan|Waitlist>  $model
     */
    #[DataProvider('itemRequests')]
    public function test_it_reports_and_filters_item_requests_by_item_type(string $source, string $model, string $itemColumn): void
    {
        $admin = $this->admin();
        $student = Student::factory()->create();
        $model::factory()->forStudent($student)
            ->forAssistiveTechnology(Item::factory()->assistiveTechnology()->physical()->create(['name' => 'Teclado adaptado']))
            ->create(['user_id' => $admin->id]);
        $model::factory()->forStudent($student)
            ->forAccessibleEducationalMaterial(Item::factory()->accessibleEducationalMaterial()->physical()->create(['name' => 'Livro em braile']))
            ->create(['user_id' => $admin->id]);

        $all = $this->actingAs($admin)->postJson(route('reports.run'), [
            'source' => $source,
            'columns' => ['item_type', $itemColumn, 'student'],
        ]);
        $onlyTechnologies = $this->actingAs($admin)->postJson(route('reports.run'), [
            'source' => $source,
            'columns' => ['item_type', $itemColumn],
            'filters' => [['field' => 'item_type', 'operator' => 'eq', 'value' => 'assistive_technology']],
        ]);

        $all->assertOk()
            ->assertJsonPath('total', 2)
            ->assertJsonFragment(['item_type' => 'Tecnologia Assistiva', $itemColumn => 'Teclado adaptado'])
            ->assertJsonFragment(['item_type' => 'Material Pedagógico Acessível', $itemColumn => 'Livro em braile']);
        $onlyTechnologies->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath("rows.0.{$itemColumn}", 'Teclado adaptado');
    }

    /**
     * @return array<string, array{string, class-string, string}>
     */
    public static function itemRequests(): array
    {
        return [
            'loans' => ['inclusive-radar.loans', Loan::class, 'loanable'],
            'waitlists' => ['inclusive-radar.waitlists', Waitlist::class, 'waitlistable'],
        ];
    }

    public function test_it_resolves_items_and_barriers_as_inspected_targets(): void
    {
        $admin = $this->admin();
        Inspection::factory()
            ->forAssistiveTechnology(Item::factory()->assistiveTechnology()->create(['name' => 'Mouse adaptado']))
            ->create(['user_id' => $admin->id]);
        Inspection::factory()
            ->forBarrier(Barrier::factory()->create(['name' => 'Rampa sem corrimão']))
            ->create(['user_id' => $admin->id]);

        $response = $this->actingAs($admin)->postJson(route('reports.run'), [
            'source' => 'inclusive-radar.inspections',
            'columns' => ['inspectable_type', 'inspectable', 'item.asset_code', 'barrier.priority'],
        ]);

        $response->assertOk()
            ->assertJsonPath('total', 2)
            ->assertJsonFragment(['inspectable_type' => 'Item', 'inspectable' => 'Mouse adaptado'])
            ->assertJsonFragment(['inspectable_type' => 'Barreira', 'inspectable' => 'Rampa sem corrimão']);
    }

    /**
     * @return array<string, mixed>
     */
    private function metadata(string $source): array
    {
        return $this->actingAs($this->admin())
            ->getJson(route('reports.metadata', ['source' => $source]))
            ->assertOk()
            ->json();
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }
}
