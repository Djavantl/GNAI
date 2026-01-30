<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Guardian;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\Person;
use Illuminate\Support\Facades\DB;

class GuardianService
{
    public function listByStudent(int $studentId)
    {
        return Guardian::with('person')
            ->where('student_id', $studentId)
            ->get();
    }

    public function create(Student $student, array $data): Guardian
    {
        return DB::transaction(function () use ($student, $data) {

            $person = Person::create([
                'name'        => $data['name'],
                'document'    => $data['document'],
                'birth_date'  => $data['birth_date'],
                'gender'      => $data['gender'] ?? 'not_specified',
                'email'       => $data['email'],
                'phone'       => $data['phone'] ?? null,
                'address'     => $data['address'] ?? null,
            ]);

            $guardian = Guardian::create([
                'person_id'    => $person->id,
                'student_id'   => $student->id,
                'relationship'=> $data['relationship'],
            ]);

            return $guardian;
        });
    }

    public function update(Guardian $guardian, array $data): Guardian
    {
        return DB::transaction(function () use ($guardian, $data) {

            $guardian->person->update([
                'name'        => $data['name'],
                'document'    => $data['document'],
                'birth_date'  => $data['birth_date'],
                'gender'      => $data['gender'] ?? $guardian->person->gender,
                'email'       => $data['email'],
                'phone'       => $data['phone'] ?? null,
                'address'     => $data['address'] ?? null,
            ]);

            $guardian->update([
                'relationship'=> $data['relationship'],
            ]);

            return $student;
        });
    }

    public function delete(Guardian $guardian): void
    {
        DB::transaction(function () use ($guardian) {
            $person = $guardian->person;

            $guardian->delete();

            $person?->delete();
        });
    }
}
