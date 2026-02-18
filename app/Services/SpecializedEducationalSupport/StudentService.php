<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Person;
use App\Models\SpecializedEducationalSupport\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StudentService
{
    public function index(array $filters = [])
    {
        $query = Student::with('person')
            ->orderBy('created_at', 'desc');

        if (!empty($filters['name'])) {
            $query->whereHas('person', fn($q) =>
                $q->where('name', 'like', "{$filters['name']}%")
            );
        }

        if (!empty($filters['email'])) {
            $query->whereHas('person', fn($q) =>
                $q->where('email', 'like', "%{$filters['email']}%")
            );
        }

        if (!empty($filters['registration'])) {
            $query->where('registration', 'like', "%{$filters['registration']}%");
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate(10)->withQueryString();
    }

    public function show(Student $student): Student
    {
        return $student->load([
            'person',
            'guardians',
            'currentContext',
            'deficiencies',
            'peis',
            'studentCourses',
            'courses',
            'currentCourse',
            'sessions' => fn($q) => $q->with(['professional.person', 'sessionRecord'])->orderBy('session_date', 'desc'),
        ]);
    }

    /**
     * Cria Pessoa + Aluno juntos
     */
    public function create(array $data): Student
    {
        return DB::transaction(function () use ($data) {
            // 1. Processa o upload da foto se ela existir
            if (isset($data['photo']) && $data['photo'] instanceof \Illuminate\Http\UploadedFile) {
                $data['photo'] = $data['photo']->store('photos', 'public');
            }

            // 2. Cria a Pessoa (incluindo o caminho da foto)
            $person = Person::create([
                'name'       => $data['name'],
                'document'   => $data['document'],
                'birth_date' => $data['birth_date'],
                'gender'     => $data['gender'] ?? 'not_specified',
                'email'      => $data['email'],
                'phone'      => $data['phone'] ?? null,
                'address'    => $data['address'] ?? null,
                'photo'      => $data['photo'] ?? null, // <--- Faltava isso
            ]);

            // 3. Cria o Aluno vinculado à pessoa
            return Student::create([
                'person_id'    => $person->id,
                'registration' => $data['registration'],
                'entry_date'   => $data['entry_date'],
            ]);
        });
    }

    /**
     * Atualiza Pessoa + Aluno
     */
    public function update(Student $student, array $data): Student
    {
        return DB::transaction(function () use ($student, $data) {
            $person = $student->person;

            // Lógica de Foto
            if (isset($data['photo']) && $data['photo'] instanceof \Illuminate\Http\UploadedFile) {
                // Se já existia uma foto antiga, deletamos o arquivo físico no storage
                if ($person->photo) {
                    \Storage::disk('public')->delete($person->photo);
                }
                // Sobe a nova foto
                $data['photo'] = $data['photo']->store('photos', 'public');
            } 
            // Caso você adicione um checkbox "remover_foto" no formulário:
            elseif (!empty($data['remove_photo'])) {
                if ($person->photo) {
                    \Storage::disk('public')->delete($person->photo);
                }
                $data['photo'] = null;
            } else {
                // Se não enviou nada e não pediu pra remover, mantemos a que já estava
                $data['photo'] = $person->photo;
            }

            // Atualiza a Pessoa
            $person->update([
                'name'       => $data['name'],
                'document'   => $data['document'],
                'birth_date' => $data['birth_date'],
                'gender'     => $data['gender'] ?? $person->gender,
                'email'      => $data['email'],
                'phone'      => $data['phone'] ?? null,
                'address'    => $data['address'] ?? null,
                'photo'      => $data['photo'], // <--- Atualiza com o novo caminho ou null
            ]);

            // Atualiza o Aluno
            $student->update([
                'registration' => $data['registration'],
                'entry_date'   => $data['entry_date'],
                'status'       => $data['status'] ?? $student->status,
            ]);

            return $student;
        });
    }

    public function delete(Student $student): void
    {
        DB::transaction(function () use ($student) {
            if ($student->person->photo) {
                \Storage::disk('public')->delete($student->person->photo);
            }
            
            $student->delete();
        });
    }
}
