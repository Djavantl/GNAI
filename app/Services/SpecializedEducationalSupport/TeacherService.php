<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Person;
use App\Models\SpecializedEducationalSupport\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\SpecializedEducationalSupport\Course;
use App\Models\SpecializedEducationalSupport\Discipline;
use App\Models\SpecializedEducationalSupport\TeacherCourseDiscipline;
use DomainException;

class TeacherService
{
    public function index(array $filters = [])
    {
        return Teacher::query()
            ->with('person')
            ->name($filters['name'] ?? null)
            ->email($filters['email'] ?? null)
            ->registration($filters['registration'] ?? null)
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();
    }

    public function show(Teacher $teacher)
    {
        return $teacher->load([
            'person',
            'courses',
            'courseDisciplines.discipline',
            'courseDisciplines.course',
        ]);
    }
    /**
     * Cria Pessoa + Professor + Usuario + Disciplinas
     */
    public function create(array $data): Teacher
    {
        return DB::transaction(function () use ($data) {
            // 1. Processa a foto (Reutilizando sua lógica)
            if (isset($data['photo']) && $data['photo'] instanceof \Illuminate\Http\UploadedFile) {
                $data['photo'] = $data['photo']->store('photos', 'public');
            }

            // 2. Cria a Pessoa
            $person = Person::create([
                'name'       => $data['name'],
                'document'   => $data['document'],
                'birth_date' => $data['birth_date'],
                'gender'     => $data['gender'] ?? 'not_specified',
                'email'      => $data['email'],
                'phone'      => $data['phone'] ?? null,
                'address'    => $data['address'] ?? null,
                'photo'      => $data['photo'] ?? null,
            ]);

            // 3. Cria o Professor
            $teacher = Teacher::create([
                'person_id'    => $person->id,
                'registration' => $data['registration'],
            ]);

            // 5. Cria o Usuário de acesso vinculado ao Teacher
            User::create([
                'name'        => $person->name, 
                'email'       => $person->email,
                'password'    => Hash::make('napne2026'), 
                'teacher_id'  => $teacher->id,
                'is_admin'    => false,
            ]);

            return $teacher;
        });
    }

    /**
     * Atualiza Professor e suas relações
     */
    public function update(Teacher $teacher, array $data): Teacher
    {
        return DB::transaction(function () use ($teacher, $data) {
            $person = $teacher->person;

            // Lógica de foto
            if (isset($data['photo']) && $data['photo'] instanceof \Illuminate\Http\UploadedFile) {
                if ($person->photo) {
                    Storage::disk('public')->delete($person->photo);
                }
                $data['photo'] = $data['photo']->store('photos', 'public');
            } elseif (!empty($data['remove_photo'])) {
                if ($person->photo) {
                    Storage::disk('public')->delete($person->photo);
                }
                $data['photo'] = null;
            } else {
                $data['photo'] = $person->photo;
            }

            // Atualiza Pessoa
            $person->update([
                'name'       => $data['name'],
                'document'   => $data['document'],
                'birth_date' => $data['birth_date'],
                'gender'     => $data['gender'] ?? $person->gender,
                'email'      => $data['email'],
                'phone'      => $data['phone'] ?? null,
                'address'    => $data['address'] ?? null,
                'photo'      => $data['photo'],
            ]);

            // Atualiza Professor
            $teacher->update([
                'registration' => $data['registration'],
            ]);

            $user = User::where('teacher_id', $teacher->id)->first();

            if ($user) {
                $user->update([
                    'name' => $person->name,
                    'email' => $person->email,
                ]);
            }

            return $teacher;
        });
    }

    /**
     * Remove o Professor e limpa arquivos
     */
    public function delete(Teacher $teacher): void
    {
        // Verifica se o professor possui vínculos com disciplinas de PEIs
        if ($teacher->peiDisciplines()->exists()) {
            throw new DomainException(
                "Não é possível excluir este professor pois ele possui registros de acompanhamento em Planos Educacionais Individualizados (PEI)."
            );
        }

        DB::transaction(function () use ($teacher) {
            // Remove foto física
            if ($teacher->person && $teacher->person->photo) {
                Storage::disk('public')->delete($teacher->person->photo);
            }

            // Deleta o usuário vinculado primeiro (se existir)
            // Isso evita erros de integridade se a FK não for cascade
            $teacher->user()->delete();

            $teacher->person->delete(); 
            $teacher->delete();
        });
    }

    /**
     * Retorna apenas os IDs das permissões globais atuais
     */
    public function getGlobalPermissionsIds(): array
    {
        return \DB::table('teacher_global_permissions')
            ->pluck('permission_id')
            ->toArray();
    }

    /**
     * Atualiza a tabela global de permissões (limpa e insere as novas)
     */
    public function updateGlobalPermissions(array $permissionIds): void
    {
        DB::transaction(function () use ($permissionIds) {

            DB::table('teacher_global_permissions')->delete();

            $permissionIds = array_unique($permissionIds);

            if (count($permissionIds)) {
                DB::table('teacher_global_permissions')->insert(
                    collect($permissionIds)->map(fn($id) => [
                        'permission_id' => $id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])->toArray()
                );
            }
        });
    }



    public function syncGrade(Teacher $teacher, array $assignments): void
    {
        DB::transaction(function () use ($teacher, $assignments) {
            TeacherCourseDiscipline::where('teacher_id', $teacher->id)->delete();

            foreach ($assignments as $courseId => $disciplineIds) {
                $course = Course::findOrFail($courseId);
                $course->ensureIsActive();

                $disciplineIds = array_unique($disciplineIds);

                $validDisciplineIds = $course->disciplines()
                    ->whereIn('disciplines.id', $disciplineIds)
                    ->pluck('disciplines.id')
                    ->toArray();

                if (count($validDisciplineIds) !== count($disciplineIds)) {
                    throw new DomainException(
                        "Uma ou mais disciplinas informadas não pertencem ao curso {$course->name}."
                    );
                }

                foreach ($validDisciplineIds as $disciplineId) {
                    $discipline = Discipline::findOrFail($disciplineId);
                    $discipline->ensureIsActive();

                    TeacherCourseDiscipline::create([
                        'teacher_id'    => $teacher->id,
                        'course_id'     => $course->id,
                        'discipline_id' => $discipline->id,
                    ]);
                }
            }
        });
    }
}