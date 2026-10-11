<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Tests\Feature\Students;

use App\Domains\Auth\Domain\Models\Permission;
use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Deficiency;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Guardian;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Person;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Professional;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class StudentPagesTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = '/specialized-educational-support/students';

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->actingAs(User::factory()->create(['is_admin' => true]));
    }

    private function payload(): array
    {
        return [
            'name' => 'Aluno de Integração', 'birth_date' => '2005-03-01',
            'gender' => 'not_specified', 'email' => 'aluno@example.test',
            'registration' => 'TEST-001', 'document' => '529.982.247-25',
            'phone' => '(11) 98765-4321', 'entry_date' => '2025-01-02',
            'address' => '<p>Rua <strong>das Flores</strong>, 10</p>',
        ];
    }

    public function test_creation_and_editing_receive_real_options_dates_and_photo_urls(): void
    {
        $student = Student::factory()->active()->create(['entry_date' => '2025-02-01']);
        $this->get(self::BASE.'/create')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('SpecializedEducationalSupport/Students/Create')
            ->where('defaultGender', 'not_specified')->where('permissions', fn ($permissions) => $permissions['student.create'] === true)
            ->has('genders')->has('studentStatuses'));
        $this->get(self::BASE.'/'.$student->id.'/edit')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('SpecializedEducationalSupport/Students/Edit')
            ->where('student.entry_date', '2025-02-01')
            ->where('student.person.birth_date', $student->person->birth_date->toDateString())
            ->where('student.person.photo_url', $student->person->photo_url)
            ->where('student.status', 'active')->has('genders'));
    }

    public function test_multipart_creation_update_and_photo_removal_preserve_redirects_and_flash(): void
    {
        Storage::fake('public');
        // A real PNG avoids depending on GD in the test environment.
        $photo = UploadedFile::fake()->createWithContent('photo.png', base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jD1kAAAAASUVORK5CYII='
        ));
        $this->post(self::BASE.'/store', $this->payload() + ['photo' => $photo])
            ->assertSessionHasNoErrors()->assertSessionHas('success', 'Aluno cadastrado com sucesso.');
        $student = Student::query()->sole();
        $path = $student->person->getRawOriginal('photo');
        Storage::disk('public')->assertExists($path);
        $this->assertDatabaseHas('people', ['document' => '52998224725', 'phone' => '11987654321']);
        $this->get(self::BASE.'/'.$student->id.'/show')->assertInertia(fn (Assert $page) => $page
            ->where('student.person.address_html', '<p>Rua <strong>das Flores</strong>, 10</p>'));

        $this->post(self::BASE.'/'.$student->id, array_replace($this->payload(), [
            '_method' => 'put', 'status' => 'locked', 'remove_photo' => '1', 'entry_date' => '',
            'address' => '<p>Endereço <em>atualizado</em></p>',
        ]))->assertRedirect(self::BASE.'/'.$student->id.'/show')
            ->assertSessionHasNoErrors()->assertSessionHas('success', 'Aluno atualizado com sucesso.');
        $this->assertDatabaseHas('students', ['id' => $student->id, 'status' => 'locked', 'entry_date' => null]);
        $this->assertDatabaseHas('people', ['id' => $student->person_id, 'photo' => null]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_validation_errors_are_available_to_inertia_and_no_invalid_student_is_saved(): void
    {
        $this->from(self::BASE.'/create')->post(self::BASE.'/store', array_replace($this->payload(), [
            'name' => 'A', 'birth_date' => '2999-01-01', 'email' => 'invalid', 'document' => '111.111.111-11',
        ]))->assertRedirect(self::BASE.'/create')->assertSessionHasErrors(['name', 'birth_date', 'email', 'document']);
        $this->get(self::BASE.'/create')->assertInertia(fn (Assert $page) => $page
            ->has('errors.name')->has('errors.birth_date')->has('errors.email')->has('errors.document'));
        $this->assertDatabaseCount('students', 0);
    }

    public function test_filters_and_pagination_work_on_inertia_partial_reloads(): void
    {
        Student::factory()->count(3)->active()->for(Person::factory()->state(['name' => 'Ana Teste']), 'person')->create();
        Student::factory()->active()->for(Person::factory()->state(['name' => 'Bruno Teste']), 'person')->create();
        $initial = $this->get(self::BASE)->assertOk();
        $this->get(self::BASE.'?name=Ana&status=active&course_id=0&deficiency_id=0&per_page=2&page=2', [
            'X-Inertia' => 'true', 'X-Inertia-Version' => $initial->viewData('page')['version'],
            'X-Inertia-Partial-Component' => 'SpecializedEducationalSupport/Students/Index',
            'X-Inertia-Partial-Data' => 'students,filters', 'X-Requested-With' => 'XMLHttpRequest',
        ])->assertOk()->assertJsonPath('props.students.total', 3)
            ->assertJsonPath('props.students.current_page', 2)->assertJsonCount(1, 'props.students.data')
            ->assertJsonPath('props.students.data.0.person.name', 'Ana Teste')
            ->assertJsonPath('props.filters.course_id', '0')->assertJsonMissingPath('props.auth');
    }

    public function test_record_includes_real_related_labels_and_safe_formatted_address(): void
    {
        $student = Student::factory()->active()->for(Person::factory()->state([
            'address' => '<p onclick="alert(1)">Rua <b>Central</b></p><script>alert(1)</script>&lt;img src=x onerror=alert(1)&gt;',
        ]), 'person')->create();
        $deficiency = Deficiency::factory()->create();
        $student->deficiencies()->attach($deficiency, ['severity' => 'moderate']);
        $guardian = Guardian::factory()->create(['student_id' => $student->id]);
        $this->get(self::BASE.'/'.$student->id.'/show')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('SpecializedEducationalSupport/Students/Show')
            ->where('student.deficiencies.0.severity_label', 'Moderada')
            ->where('student.guardians.0.relationship_label', $guardian->relationshipLabel())
            ->where('student.guardians.0.person.photo_url', $guardian->person->photo_url)
            ->where('student.person.address_html', '<p>Rua <b>Central</b></p>&lt;img src=x onerror=alert(1)&gt;')
            ->has('student.student_courses', 0)->has('aeeEvaluations', 0)->has('pedagogicalRecords', 0));
    }

    public function test_view_only_users_cannot_mutate_and_do_not_receive_restricted_sections(): void
    {
        $professional = Professional::factory()->active()->create();
        $permission = Permission::query()->create(['name' => 'Ver alunos', 'slug' => 'student.view']);
        $professional->position->permissions()->attach($permission);
        $user = User::factory()->create(['is_admin' => false, 'professional_id' => $professional->id]);
        $this->actingAs($user);
        $student = Student::factory()->active()->create();
        Guardian::factory()->create(['student_id' => $student->id]);
        $this->get(self::BASE.'/'.$student->id.'/show')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->where('permissions', fn ($permissions) => $permissions['student.view'] === true
                && $permissions['student.update'] === false && $permissions['student.delete'] === false)->missing('student.guardians')
            ->missing('student.deficiencies')->missing('student.documents')->missing('student.peis')
            ->has('aeeEvaluations', 0)->has('pedagogicalRecords', 0));
        $this->get(self::BASE.'/create')->assertForbidden();
        $this->get(self::BASE.'/'.$student->id.'/edit')->assertForbidden();
        $this->delete(self::BASE.'/'.$student->id)->assertForbidden();
        $this->assertDatabaseHas('students', ['id' => $student->id]);
    }

    public function test_deletion_succeeds_without_links_and_preserves_linked_students_with_error_flash(): void
    {
        $student = Student::factory()->active()->create();
        $this->delete(self::BASE.'/'.$student->id)->assertRedirect(self::BASE)
            ->assertSessionHas('success', 'Aluno removido com sucesso.');
        $this->assertDatabaseMissing('students', ['id' => $student->id]);

        $linked = Student::factory()->active()->create();
        Guardian::factory()->create(['student_id' => $linked->id]);
        $this->from(self::BASE.'/'.$linked->id.'/show')->delete(self::BASE.'/'.$linked->id)
            ->assertRedirect(self::BASE.'/'.$linked->id.'/show')->assertSessionHas('error');
        $this->assertDatabaseHas('students', ['id' => $linked->id]);
    }
}
