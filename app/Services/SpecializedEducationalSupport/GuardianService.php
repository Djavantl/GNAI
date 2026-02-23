<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Guardian;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\Person;
use Illuminate\Support\Facades\DB;

class GuardianService
{
    public function getByStudent(Student $student, array $filters = [])
    {
        return Guardian::query()
            ->with('person') // Eager loading essencial
            ->where('student_id', $student->id)
            ->name($filters['name'] ?? null)
            ->email($filters['email'] ?? null)
            ->relationship($filters['relationship'] ?? null)
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    public function show(Guardian $guardian){
        return $guardian->load('person', 'student.person');
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

            return $guardian;
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
