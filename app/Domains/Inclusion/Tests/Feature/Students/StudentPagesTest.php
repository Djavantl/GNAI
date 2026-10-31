<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Feature\Students;

use App\Domains\Auth\Domain\Models\Permission;
use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Domain\Models\Deficiency;
use App\Domains\Inclusion\Domain\Models\Guardian;
use App\Domains\Inclusion\Domain\Models\Person;
use App\Domains\Inclusion\Domain\Models\Professional;
use App\Domains\Inclusion\Domain\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class StudentPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->actingAs(User::factory()->create(['is_admin' => true]));
    }

    public function test_it_renders_the_index_with_filtered_and_paginated_students(): void
    {
        Student::factory()->count(3)->active()->for(Person::factory()->state(['name' => 'Ana Teste']), 'person')->create();
        Student::factory()->active()->for(Person::factory()->state(['name' => 'Bruno Teste']), 'person')->create();

        $response = $this->get(route('specialized-educational-support.students.index', [
            'name' => 'Ana',
            'per_page' => 2,
            'page' => 2,
        ]));

        $response->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Inclusion/Students/Index')
            ->where('students.total', 3)
            ->where('students.current_page', 2)
            ->has('students.data', 1)
            ->where('students.data.0.person.name', 'Ana Teste')
            ->where('filters.name', 'Ana'));
    }

    public function test_it_renders_the_create_and_edit_forms_with_their_contracts(): void
    {
        $student = Student::factory()->active()->create(['entry_date' => '2025-02-01']);

        $create = $this->get(route('specialized-educational-support.students.create'));
        $edit = $this->get(route('specialized-educational-support.students.edit', $student));

        $create->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Inclusion/Students/Create')
            ->where('defaultGender', 'not_specified')
            ->has('genders')
            ->has('studentStatuses'));
        $edit->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Inclusion/Students/Edit')
            ->where('student.entry_date', '2025-02-01')
            ->where('student.person.birth_date', $student->person->birth_date->toDateString())
            ->where('student.person.photo_url', $student->person->photo_url)
            ->where('student.status', 'active'));
    }

    public function test_it_creates_and_updates_a_student_with_photo_upload_and_removal(): void
    {
        Storage::fake('public');
        // A real PNG avoids depending on GD in the test environment.
        $photo = UploadedFile::fake()->createWithContent('photo.png', base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jD1kAAAAASUVORK5CYII='
        ));

        $this->post(route('specialized-educational-support.students.store'), $this->payload() + ['photo' => $photo])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Aluno cadastrado com sucesso.');

        $student = Student::query()->sole();
        $path = $student->person->getRawOriginal('photo');
        Storage::disk('public')->assertExists($path);
        $this->assertDatabaseHas('people', ['document' => '52998224725', 'phone' => '11987654321']);

        $this->put(route('specialized-educational-support.students.update', $student), array_replace($this->payload(), [
            'status' => 'locked',
            'remove_photo' => '1',
            'entry_date' => '',
        ]))
            ->assertRedirect(route('specialized-educational-support.students.show', $student))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Aluno atualizado com sucesso.');

        $this->assertDatabaseHas('students', ['id' => $student->id, 'status' => 'locked', 'entry_date' => null]);
        $this->assertDatabaseHas('people', ['id' => $student->person_id, 'photo' => null]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_it_rejects_invalid_data_without_saving_the_student(): void
    {
        $response = $this->from(route('specialized-educational-support.students.create'))
            ->post(route('specialized-educational-support.students.store'), array_replace($this->payload(), [
                'name' => 'A',
                'birth_date' => '2999-01-01',
                'email' => 'invalid',
                'document' => '111.111.111-11',
            ]));

        $response->assertRedirect(route('specialized-educational-support.students.create'))
            ->assertSessionHasErrors(['name', 'birth_date', 'email', 'document']);
        $this->assertDatabaseCount('students', 0);
    }

    public function test_it_shows_related_labels_and_a_sanitized_address(): void
    {
        $student = Student::factory()->active()->for(Person::factory()->state([
            'address' => '<p onclick="alert(1)">Rua <b>Central</b></p><script>alert(1)</script>&lt;img src=x onerror=alert(1)&gt;',
        ]), 'person')->create();
        $student->deficiencies()->attach(Deficiency::factory()->create(), ['severity' => 'moderate']);
        $guardian = Guardian::factory()->create(['student_id' => $student->id]);

        $response = $this->get(route('specialized-educational-support.students.show', $student));

        $response->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Inclusion/Students/Show')
            ->where('student.deficiencies.0.severity_label', 'Moderada')
            ->where('student.guardians.0.relationship_label', $guardian->relationshipLabel())
            ->where('student.person.address_html', '<p>Rua <b>Central</b></p>&lt;img src=x onerror=alert(1)&gt;'));
    }

    public function test_it_restricts_view_only_users_to_the_allowed_sections(): void
    {
        $professional = Professional::factory()->active()->create();
        $professional->position->permissions()->attach(
            Permission::query()->create(['name' => 'Ver alunos', 'slug' => 'student.view']),
        );
        $this->actingAs(User::factory()->create(['is_admin' => false, 'professional_id' => $professional->id]));
        $student = Student::factory()->active()->create();
        Guardian::factory()->create(['student_id' => $student->id]);

        $show = $this->get(route('specialized-educational-support.students.show', $student));

        $show->assertOk()->assertInertia(fn (Assert $page) => $page
            ->where('permissions', fn ($permissions) => $permissions['student.view'] === true
                && $permissions['student.update'] === false
                && $permissions['student.delete'] === false)
            ->missing('student.guardians')
            ->missing('student.deficiencies')
            ->missing('student.documents')
            ->missing('student.peis'));
        $this->get(route('specialized-educational-support.students.create'))->assertForbidden();
        $this->get(route('specialized-educational-support.students.edit', $student))->assertForbidden();
        $this->delete(route('specialized-educational-support.students.destroy', $student))->assertForbidden();
        $this->assertDatabaseHas('students', ['id' => $student->id]);
    }

    public function test_it_deletes_only_students_without_links(): void
    {
        $student = Student::factory()->active()->create();
        $linked = Student::factory()->active()->create();
        Guardian::factory()->create(['student_id' => $linked->id]);

        $this->delete(route('specialized-educational-support.students.destroy', $student))
            ->assertRedirect(route('specialized-educational-support.students.index'))
            ->assertSessionHas('success', 'Aluno removido com sucesso.');
        $this->from(route('specialized-educational-support.students.show', $linked))
            ->delete(route('specialized-educational-support.students.destroy', $linked))
            ->assertRedirect(route('specialized-educational-support.students.show', $linked))
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('students', ['id' => $student->id]);
        $this->assertDatabaseHas('students', ['id' => $linked->id]);
    }

    /**
     * @return array<string, string>
     */
    private function payload(): array
    {
        return [
            'name' => 'Aluno de Integração',
            'birth_date' => '2005-03-01',
            'gender' => 'not_specified',
            'email' => 'aluno@example.test',
            'registration' => 'TEST-001',
            'document' => '529.982.247-25',
            'phone' => '(11) 98765-4321',
            'entry_date' => '2025-01-02',
            'address' => '<p>Rua <strong>das Flores</strong>, 10</p>',
        ];
    }
}
