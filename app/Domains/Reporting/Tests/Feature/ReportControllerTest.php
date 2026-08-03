<?php

declare(strict_types=1);

namespace App\Domains\Reporting\Tests\Feature;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Backup\Domain\Enums\BackupStatus;
use App\Domains\Backup\Domain\Models\Backup;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Domains\InclusiveRadar\Domain\Models\Loan;
use App\Domains\InclusiveRadar\Domain\Models\Waitlist;
use App\Domains\Reporting\Application\Services\ReportCatalog;
use App\Domains\Reporting\Application\Services\ReportRelationCatalog;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\StudentStatus;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Deficiency;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_exposes_the_students_report_source(): void
    {
        $response = $this->actingAs($this->admin())->getJson(route('reports.sources'));

        $response
            ->assertOk()
            ->assertJsonFragment([
                'key' => 'specialized-support.students',
                'label' => 'Alunos',
            ]);
    }

    public function test_catalog_exposes_primary_sources_and_hides_relation_only_sources(): void
    {
        $response = $this->actingAs($this->admin())->getJson(route('reports.sources'));

        $response
            ->assertOk()
            ->assertJsonCount(31)
            ->assertJsonFragment(['key' => 'auth.users'])
            ->assertJsonFragment(['key' => 'backup.backups'])
            ->assertJsonFragment(['key' => 'inclusive-radar.loans'])
            ->assertJsonFragment(['key' => 'inclusive-radar.inspections'])
            ->assertJsonFragment(['key' => 'specialized-support.pei-disciplines'])
            ->assertJsonMissing(['key' => 'specialized-support.student-deficiencies']);
    }

    public function test_every_discovered_source_provides_metadata_and_can_execute(): void
    {
        $admin = $this->admin();
        $sources = $this->actingAs($admin)
            ->getJson(route('reports.sources'))
            ->assertOk()
            ->json();

        foreach ($sources as $source) {
            $metadata = $this->actingAs($admin)
                ->getJson(route('reports.metadata', ['source' => $source['key']]))
                ->assertOk()
                ->json();

            $firstColumn = array_key_first($metadata['columns']);

            $this->actingAs($admin)
                ->postJson(route('reports.run'), [
                    'source' => $source['key'],
                    'columns' => [$firstColumn],
                    'limit' => 1,
                ])
                ->assertOk()
                ->assertJsonStructure(['rows', 'headers', 'total']);
        }
    }

    public function test_metadata_only_exposes_explicit_columns_and_filters(): void
    {
        $response = $this->actingAs($this->admin())->getJson(route('reports.metadata', [
            'source' => 'specialized-support.students',
        ]));

        $response
            ->assertOk()
            ->assertJsonPath('columns.registration', 'Matrícula')
            ->assertJsonMissing(['key' => 'password'])
            ->assertJsonFragment(['key' => 'status', 'type' => 'select']);
    }

    public function test_inspections_metadata_exposes_each_polymorphic_target_as_a_relation(): void
    {
        $metadata = $this->actingAs($this->admin())->getJson(route('reports.metadata', [
            'source' => 'inclusive-radar.inspections',
        ]))->assertOk()->json();

        $relations = collect($metadata['relations'])->keyBy('name');

        self::assertSame('Tecnologia assistiva', $relations['assistiveTechnology']['label']);
        self::assertSame('Material pedagógico acessível', $relations['accessibleEducationalMaterial']['label']);
        self::assertSame('Barreira', $relations['barrier']['label']);
        self::assertArrayNotHasKey('name', $relations['assistiveTechnology']['columns']);
        self::assertArrayHasKey('asset_code', $relations['assistiveTechnology']['columns']);
        self::assertArrayHasKey('asset_code', $relations['accessibleEducationalMaterial']['columns']);
        self::assertArrayHasKey('priority', $relations['barrier']['columns']);
    }

    public function test_resource_inspections_do_not_repeat_the_inspected_resource(): void
    {
        foreach ([
            'inclusive-radar.assistive-technologies',
            'inclusive-radar.accessible-educational-materials',
            'inclusive-radar.barriers',
        ] as $source) {
            $metadata = $this->actingAs($this->admin())->getJson(route('reports.metadata', [
                'source' => $source,
            ]))->assertOk()->json();
            $inspections = collect($metadata['relations'])->firstWhere('name', 'inspections');

            self::assertArrayNotHasKey('inspectable_type', $inspections['columns']);
            self::assertArrayNotHasKey('inspectable', $inspections['columns']);
            self::assertArrayHasKey('inspection_date', $inspections['columns']);
        }
    }

    public function test_inspection_evidence_relation_only_exposes_file_fields(): void
    {
        $metadata = $this->actingAs($this->admin())->getJson(route('reports.metadata', [
            'source' => 'inclusive-radar.inspections',
        ]))->assertOk()->json();

        $evidences = collect($metadata['relations'])->firstWhere('name', 'evidences');

        self::assertSame([
            'original_name' => 'Nome do arquivo',
            'mime_type' => 'Formato',
            'size' => 'Tamanho (bytes)',
        ], $evidences['columns']);
        self::assertArrayNotHasKey('inspectable_type', $evidences['columns']);
        self::assertArrayNotHasKey('inspectable', $evidences['columns']);
    }

    public function test_inspection_evidence_item_type_uses_translated_options(): void
    {
        $metadata = $this->actingAs($this->admin())->getJson(route('reports.metadata', [
            'source' => 'inclusive-radar.inspection-evidences',
        ]))->assertOk()->json();
        $definitions = collect($metadata['column_definitions'])->keyBy('key');

        self::assertSame('select', $definitions['inspectable_type']['type']);
        self::assertSame('Tecnologia assistiva', $definitions['inspectable_type']['options']['assistive_technology']);
        self::assertSame('Material pedagógico acessível', $definitions['inspectable_type']['options']['accessible_educational_material']);
        self::assertSame('Barreira', $definitions['inspectable_type']['options']['barrier']);
    }

    public function test_it_runs_students_report_with_allowed_filter_and_formats_values(): void
    {
        Student::factory()->create([
            'registration' => 'MAT000001',
            'status' => StudentStatus::ACTIVE,
            'is_repeater' => true,
        ]);
        Student::factory()->create([
            'registration' => 'MAT000002',
            'status' => StudentStatus::DROPPED,
        ]);

        $response = $this->actingAs($this->admin())->postJson(route('reports.run'), [
            'source' => 'specialized-support.students',
            'columns' => ['registration', 'status', 'is_repeater'],
            'filters' => [[
                'field' => 'status',
                'operator' => 'eq',
                'value' => 'active',
            ]],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('rows.0.registration', 'MAT000001')
            ->assertJsonPath('rows.0.status', 'Ativo')
            ->assertJsonPath('rows.0.is_repeater', 'Sim');
    }

    public function test_students_metadata_exposes_reportable_relations(): void
    {
        $response = $this->actingAs($this->admin())->getJson(route('reports.metadata', [
            'source' => 'specialized-support.students',
        ]));

        $response
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'deficiencies',
                'label' => 'Deficiências',
            ])
            ->assertJsonFragment([
                'severity' => 'Severidade',
                'notes' => 'Observações',
            ]);
    }

    public function test_students_metadata_uses_history_relations_without_current_duplicates(): void
    {
        $metadata = $this->actingAs($this->admin())->getJson(route('reports.metadata', [
            'source' => 'specialized-support.students',
        ]))->assertOk()->json();

        $relations = collect($metadata['relations'])->keyBy('name');

        self::assertSame('Contextos dos alunos', $relations['contexts']['label']);
        self::assertFalse($relations->has('person'));
        self::assertFalse($relations->has('currentContext'));
        self::assertContains('is_current', array_column($relations['contexts']['column_definitions'], 'key'));
        self::assertSame('Matrículas em cursos', $relations['studentCourses']['label']);
        self::assertFalse($relations->has('currentCourse'));
        self::assertArrayNotHasKey('student', $relations['contexts']['columns']);
        self::assertArrayNotHasKey('registration', $relations['contexts']['columns']);
        self::assertArrayNotHasKey('student', $relations['studentCourses']['columns']);
        self::assertArrayNotHasKey('registration', $relations['studentCourses']['columns']);
        self::assertContains('is_current', array_column($relations['studentCourses']['column_definitions'], 'key'));
        self::assertArrayNotHasKey('student', $relations['guardians']['columns']);
    }

    public function test_guardians_relation_does_not_repeat_the_student_name(): void
    {
        $metadata = $this->actingAs($this->admin())->getJson(route('reports.metadata', [
            'source' => 'specialized-support.guardians',
        ]))->assertOk()->json();

        $relations = collect($metadata['relations'])->keyBy('name');

        self::assertArrayNotHasKey('name', $relations['student']['columns']);
        self::assertArrayHasKey('registration', $relations['student']['columns']);
    }

    public function test_guardians_and_people_relations_do_not_repeat_person_fields(): void
    {
        $guardianMetadata = $this->actingAs($this->admin())->getJson(route('reports.metadata', [
            'source' => 'specialized-support.guardians',
        ]))->assertOk()->json();
        $guardianRelations = collect($guardianMetadata['relations'])->keyBy('name');

        self::assertArrayNotHasKey('name', $guardianRelations['person']['columns']);
        self::assertArrayNotHasKey('document', $guardianRelations['person']['columns']);
        self::assertArrayHasKey('birth_date', $guardianRelations['person']['columns']);

        $peopleMetadata = $this->actingAs($this->admin())->getJson(route('reports.metadata', [
            'source' => 'specialized-support.people',
        ]))->assertOk()->json();
        $peopleRelations = collect($peopleMetadata['relations'])->keyBy('name');

        self::assertArrayNotHasKey('name', $peopleRelations['guardians']['columns']);
        self::assertArrayNotHasKey('document', $peopleRelations['guardians']['columns']);
        self::assertArrayHasKey('relationship', $peopleRelations['guardians']['columns']);
        self::assertArrayHasKey('student', $peopleRelations['guardians']['columns']);
    }

    public function test_users_and_professionals_do_not_repeat_identity_fields(): void
    {
        $users = $this->actingAs($this->admin())->getJson(route('reports.metadata', [
            'source' => 'auth.users',
        ]))->assertOk()->json();
        $userRelations = collect($users['relations'])->keyBy('name');

        self::assertArrayNotHasKey('name', $userRelations['professional']['columns']);
        self::assertArrayNotHasKey('email', $userRelations['professional']['columns']);
        self::assertArrayHasKey('registration', $userRelations['professional']['columns']);

        $professionals = $this->actingAs($this->admin())->getJson(route('reports.metadata', [
            'source' => 'specialized-support.professionals',
        ]))->assertOk()->json();
        $professionalRelations = collect($professionals['relations'])->keyBy('name');

        self::assertArrayNotHasKey('name', $professionalRelations['user']['columns']);
        self::assertArrayNotHasKey('email', $professionalRelations['user']['columns']);
        self::assertArrayHasKey('role', $professionalRelations['user']['columns']);
    }

    public function test_students_and_professionals_expose_loans_and_waitlists(): void
    {
        foreach (['specialized-support.students', 'specialized-support.professionals'] as $source) {
            $metadata = $this->actingAs($this->admin())->getJson(route('reports.metadata', [
                'source' => $source,
            ]))->assertOk()->json();
            $relations = collect($metadata['relations'])->keyBy('name');

            self::assertSame('Empréstimos', $relations['loans']['label']);
            self::assertSame('Listas de espera', $relations['waitlists']['label']);
        }

        $studentMetadata = $this->actingAs($this->admin())->getJson(route('reports.metadata', [
            'source' => 'specialized-support.students',
        ]))->assertOk()->json();
        $studentRelations = collect($studentMetadata['relations'])->keyBy('name');
        self::assertArrayNotHasKey('student', $studentRelations['loans']['columns']);
        self::assertArrayNotHasKey('student', $studentRelations['waitlists']['columns']);

        $professionalMetadata = $this->actingAs($this->admin())->getJson(route('reports.metadata', [
            'source' => 'specialized-support.professionals',
        ]))->assertOk()->json();
        $professionalRelations = collect($professionalMetadata['relations'])->keyBy('name');
        self::assertArrayNotHasKey('professional', $professionalRelations['loans']['columns']);
        self::assertArrayNotHasKey('professional', $professionalRelations['waitlists']['columns']);
    }

    public function test_student_contexts_and_peis_only_repeat_independent_version_fields(): void
    {
        $contextMetadata = $this->actingAs($this->admin())->getJson(route('reports.metadata', [
            'source' => 'specialized-support.student-contexts',
        ]))->assertOk()->json();
        $contextRelations = collect($contextMetadata['relations'])->keyBy('name');

        self::assertArrayNotHasKey('student', $contextRelations['peis']['columns']);
        self::assertArrayNotHasKey('registration', $contextRelations['peis']['columns']);
        self::assertArrayNotHasKey('semester', $contextRelations['peis']['columns']);
        self::assertSame('Versão do PEI', $contextRelations['peis']['columns']['version']);

        $peiMetadata = $this->actingAs($this->admin())->getJson(route('reports.metadata', [
            'source' => 'specialized-support.peis',
        ]))->assertOk()->json();
        $peiRelations = collect($peiMetadata['relations'])->keyBy('name');

        self::assertArrayNotHasKey('student', $peiRelations['studentContext']['columns']);
        self::assertArrayNotHasKey('registration', $peiRelations['studentContext']['columns']);
        self::assertArrayNotHasKey('semester', $peiRelations['studentContext']['columns']);
        self::assertSame('Versão do contexto', $peiRelations['studentContext']['columns']['version']);
    }

    public function test_semester_relations_do_not_repeat_period_parts_already_shown_by_the_parent(): void
    {
        foreach ([
            'specialized-support.student-documents',
            'specialized-support.student-contexts',
            'specialized-support.peis',
        ] as $source) {
            $metadata = $this->actingAs($this->admin())->getJson(route('reports.metadata', [
                'source' => $source,
            ]))->assertOk()->json();
            $semester = collect($metadata['relations'])->firstWhere('name', 'semester');

            self::assertArrayNotHasKey('label', $semester['columns']);
            self::assertArrayNotHasKey('year', $semester['columns']);
            self::assertArrayNotHasKey('term', $semester['columns']);
            self::assertArrayHasKey('start_date', $semester['columns']);
            self::assertArrayHasKey('end_date', $semester['columns']);
        }
    }

    public function test_users_expose_loans_and_waitlists_without_repeating_the_registrant(): void
    {
        $metadata = $this->actingAs($this->admin())->getJson(route('reports.metadata', [
            'source' => 'auth.users',
        ]))->assertOk()->json();
        $relations = collect($metadata['relations'])->keyBy('name');

        self::assertSame('Empréstimos', $relations['loans']['label']);
        self::assertSame('Listas de espera', $relations['waitlists']['label']);
        self::assertArrayNotHasKey('registered_by', $relations['loans']['columns']);
        self::assertArrayNotHasKey('registered_by', $relations['waitlists']['columns']);
    }

    public function test_no_relation_repeats_a_field_already_flattened_by_its_parent(): void
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

    public function test_no_source_exposes_duplicate_relations_to_the_same_model(): void
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

    public function test_students_report_selects_and_filters_deficiency_fields(): void
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
            'filters' => [[
                'field' => 'deficiencies.name',
                'operator' => 'contains',
                'value' => 'visual',
            ]],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('rows.0.registration', 'MAT-REL-1')
            ->assertJsonPath('rows.0.deficiencies__name', 'Deficiência visual, Deficiência auditiva')
            ->assertJsonPath('rows.0.deficiencies__cid_code', 'H54, H90')
            ->assertJsonPath('rows.0.deficiencies__pivot__severity', 'moderate, mild');
    }

    public function test_related_records_can_be_limited_to_matching_filters(): void
    {
        $student = Student::factory()->create(['registration' => 'MAT-MODOS']);
        $visual = Deficiency::factory()->create(['name' => 'Visual']);
        $hearing = Deficiency::factory()->create(['name' => 'Auditiva']);
        $student->deficiencies()->attach($visual, ['severity' => 'moderate']);
        $student->deficiencies()->attach($hearing, ['severity' => 'mild']);

        $filtered = $this->actingAs($this->admin())->postJson(route('reports.run'), [
            'source' => 'specialized-support.students',
            'columns' => ['registration', 'deficiencies.name'],
            'filters' => [[
                'field' => 'deficiencies.name',
                'operator' => 'eq',
                'value' => 'Visual',
            ]],
            'filter_related_values' => true,
        ])->assertOk();

        $filtered->assertJsonPath('rows.0.deficiencies__name', 'Visual');

        $pivotFiltered = $this->actingAs($this->admin())->postJson(route('reports.run'), [
            'source' => 'specialized-support.students',
            'columns' => ['registration', 'deficiencies.name'],
            'filters' => [[
                'field' => 'deficiencies.pivot.severity',
                'operator' => 'eq',
                'value' => 'moderate',
            ]],
            'filter_related_values' => true,
        ])->assertOk();

        $pivotFiltered->assertJsonPath('rows.0.deficiencies__name', 'Visual');
    }

    public function test_backup_report_resolves_a_singular_user_relation(): void
    {
        $responsible = User::factory()->create([
            'name' => 'Responsável pelo backup',
            'email' => 'backup@example.test',
        ]);

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

        $response
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('rows.0.user', 'Responsável pelo backup')
            ->assertJsonPath('rows.0.user__email', 'backup@example.test');
    }

    public function test_user_profile_is_formatted_in_portuguese(): void
    {
        User::factory()->create([
            'name' => 'Professor do relatório',
            'email' => 'professor-relatorio@example.test',
            'role' => 'teacher',
        ]);

        $response = $this->actingAs($this->admin())->postJson(route('reports.run'), [
            'source' => 'auth.users',
            'columns' => ['name', 'role'],
            'filters' => [[
                'field' => 'email',
                'operator' => 'eq',
                'value' => 'professor-relatorio@example.test',
            ]],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('rows.0.role', 'Professor');
    }

    public function test_it_rejects_a_column_not_declared_by_the_source(): void
    {
        $response = $this->actingAs($this->admin())->postJson(route('reports.run'), [
            'source' => 'specialized-support.students',
            'columns' => ['person_id'],
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonPath('message', 'A coluna person_id não está disponível neste relatório.');
    }

    public function test_it_rejects_an_operator_not_declared_by_the_filter(): void
    {
        $response = $this->actingAs($this->admin())->postJson(route('reports.run'), [
            'source' => 'specialized-support.students',
            'columns' => ['registration'],
            'filters' => [[
                'field' => 'status',
                'operator' => 'contains',
                'value' => 'active',
            ]],
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonPath('message', 'O operador selecionado não é permitido para esse filtro.');
    }

    public function test_polymorphic_loan_report_resolves_each_resource_type(): void
    {
        $admin = $this->admin();
        $student = Student::factory()->create();
        $technology = AssistiveTechnology::factory()->create(['name' => 'Teclado adaptado']);
        $material = AccessibleEducationalMaterial::factory()->create(['name' => 'Livro em braile']);

        Loan::factory()->forAssistiveTechnology($technology)->forStudent($student)->create(['user_id' => $admin->id]);
        Loan::factory()->forAccessibleEducationalMaterial($material)->forStudent($student)->create(['user_id' => $admin->id]);

        $response = $this->actingAs($admin)->postJson(route('reports.run'), [
            'source' => 'inclusive-radar.loans',
            'columns' => ['loanable_type', 'loanable', 'student', 'status'],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('total', 2)
            ->assertJsonFragment(['loanable_type' => 'Tecnologia assistiva'])
            ->assertJsonFragment(['loanable_type' => 'Material pedagógico acessível'])
            ->assertJsonFragment(['loanable' => 'Teclado adaptado'])
            ->assertJsonFragment(['loanable' => 'Livro em braile']);
    }

    public function test_polymorphic_inspection_report_resolves_different_targets(): void
    {
        $admin = $this->admin();
        $technology = AssistiveTechnology::factory()->create(['name' => 'Mouse adaptado']);
        $material = AccessibleEducationalMaterial::factory()->create(['name' => 'Prancha de comunicação']);

        Inspection::factory()->forAssistiveTechnology($technology)->create(['user_id' => $admin->id]);
        Inspection::factory()->forAccessibleEducationalMaterial($material)->create(['user_id' => $admin->id]);

        $response = $this->actingAs($admin)->postJson(route('reports.run'), [
            'source' => 'inclusive-radar.inspections',
            'columns' => ['inspectable_type', 'inspectable', 'inspection_date', 'registered_by'],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('total', 2)
            ->assertJsonFragment(['inspectable_type' => 'Tecnologia assistiva'])
            ->assertJsonFragment(['inspectable_type' => 'Material pedagógico acessível'])
            ->assertJsonFragment(['inspectable' => 'Mouse adaptado'])
            ->assertJsonFragment(['inspectable' => 'Prancha de comunicação']);
    }

    public function test_polymorphic_waitlist_report_resolves_each_resource_type(): void
    {
        $admin = $this->admin();
        $student = Student::factory()->create();
        $technology = AssistiveTechnology::factory()->create(['name' => 'Acionador adaptado']);
        $material = AccessibleEducationalMaterial::factory()->create(['name' => 'Mapa tátil']);

        Waitlist::factory()->forAssistiveTechnology($technology)->forStudent($student)->create(['user_id' => $admin->id]);
        Waitlist::factory()->forAccessibleEducationalMaterial($material)->forStudent($student)->create(['user_id' => $admin->id]);

        $response = $this->actingAs($admin)->postJson(route('reports.run'), [
            'source' => 'inclusive-radar.waitlists',
            'columns' => ['waitlistable_type', 'waitlistable', 'student', 'status'],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('total', 2)
            ->assertJsonFragment(['waitlistable_type' => 'Tecnologia assistiva'])
            ->assertJsonFragment(['waitlistable_type' => 'Material pedagógico acessível'])
            ->assertJsonFragment(['waitlistable' => 'Acionador adaptado'])
            ->assertJsonFragment(['waitlistable' => 'Mapa tátil']);
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }
}
