<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Person;
use App\Models\SpecializedEducationalSupport\Student;
use Illuminate\Support\Facades\DB;

class StudentService
{
    public function all()
    {
        return Student::with('person')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Cria Pessoa + Aluno juntos
     */
    public function create(array $data): Student
    {
        return DB::transaction(function () use ($data) {

            $person = Person::create([
                'name'        => $data['name'],
                'document'    => $data['document'],
                'birth_date'  => $data['birth_date'],
                'gender'      => $data['gender'] ?? 'not_specified',
                'email'       => $data['email'],
                'phone'       => $data['phone'] ?? null,
                'address'     => $data['address'] ?? null,
            ]);

            $student = Student::create([
                'person_id'    => $person->id,
                'registration' => $data['registration'],
                'entry_date'   => $data['entry_date'],
                // status fica default (active)
            ]);

            return $student;
        });
    }

    /**
     * Atualiza Pessoa + Aluno
     */
    public function update(Student $student, array $data): Student
    {
        return DB::transaction(function () use ($student, $data) {

            $student->person->update([
                'name'        => $data['name'],
                'document'    => $data['document'],
                'birth_date'  => $data['birth_date'],
                'gender'      => $data['gender'] ?? $student->person->gender,
                'email'       => $data['email'],
                'phone'       => $data['phone'] ?? null,
                'address'     => $data['address'] ?? null,
            ]);

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
            // cascadeOnDelete já remove a pessoa
            $student->delete();
        });
    }
}
