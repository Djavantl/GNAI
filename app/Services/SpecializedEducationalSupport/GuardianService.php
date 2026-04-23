<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Guardian;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\Person;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GuardianService
{
    public function getByStudent(Student $student, array $filters = [])
    {
        return Guardian::query()
            ->with('person')
            ->where('student_id', $student->id)
            ->name($filters['name'] ?? null)
            ->email($filters['email'] ?? null)
            ->relationship($filters['relationship'] ?? null)
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    public function show(Guardian $guardian): Guardian
    {
        return $guardian->load('person', 'student.person');
    }

    public function create(Student $student, array $data): Guardian
    {
        return DB::transaction(function () use ($student, $data) {

            if (isset($data['photo']) && $data['photo'] instanceof \Illuminate\Http\UploadedFile) {
                $data['photo'] = $data['photo']->store('photos', 'public');
            } else {
                $data['photo'] = null;
            }

            $person = Person::create([
                'name'       => $data['name'],
                'document'   => $data['document'],
                'birth_date' => $data['birth_date'],
                'gender'     => $data['gender'] ?? 'not_specified',
                'email'      => $data['email'],
                'phone'      => $data['phone'] ?? null,
                'address'    => $data['address'] ?? null,
                'photo'      => $data['photo'],
            ]);

            return Guardian::create([
                'person_id'    => $person->id,
                'student_id'   => $student->id,
                'relationship' => $data['relationship'],
            ]);
        });
    }

    public function update(Guardian $guardian, array $data): Guardian
    {
        return DB::transaction(function () use ($guardian, $data) {

            $person = $guardian->person;

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

            $guardian->update([
                'relationship' => $data['relationship'],
            ]);

            return $guardian;
        });
    }

    public function delete(Guardian $guardian): void
    {
        DB::transaction(function () use ($guardian) {
            $person = $guardian->person;

            $guardian->delete();

            if ($person?->photo) {
                Storage::disk('public')->delete($person->photo);
            }

            $person?->delete();
        });
    }
}
