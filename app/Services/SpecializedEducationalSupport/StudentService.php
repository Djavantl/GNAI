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
        $query = Student::query()
            ->select('students.*')
            ->join('people', 'people.id', '=', 'students.person_id')
            ->with(['person', 'currentCourse']);

        if (auth()->user()->teacher_id) {
            $teacherId = auth()->user()->teacher_id;

            $query->whereHas('courses', function ($q) use ($teacherId) {
                $q->whereIn('courses.id', function ($sub) use ($teacherId) {
                    $sub->select('course_id')
                        ->from('teacher_courses')
                        ->where('teacher_id', $teacherId);
                });
            });
        }

        return $query
            ->name($filters['name'] ?? null)
            ->email($filters['email'] ?? null)
            ->registration($filters['registration'] ?? null)
            ->status($filters['status'] ?? null)
            ->orderBy('people.name', 'asc')
            ->paginate(10)
            ->withQueryString();
    }

    public function show(Student $student): Student
    {
        $user = auth()->user();

        return $student->load([
            'person',
            'guardians',
            'currentContext',
            'deficiencies',
            'peis' => fn ($query) =>
            $query->visibleToUser(auth()->user())
                ->with(['semester', 'discipline'])
                ->latest(),
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

            $data['entry_date'] = now()->format('Y-m-d'); 

            // 2. Cria a Pessoa (incluindo o caminho da foto)
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

            // 3. Cria o Aluno vinculado à pessoa
            return $student = Student::create([
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
