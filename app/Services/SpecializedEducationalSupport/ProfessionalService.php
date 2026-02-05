<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Person;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ProfessionalService
{
    public function index()
    {
        return Professional::with(['person', 'position'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function show(Professional $professional){
        return $professional->load('person', 'position');
    }

    /**
     * Cria Pessoa + Profissional
     */
    public function create(array $data): Professional
    {
        return DB::transaction(function () use ($data) {

            $person = Person::create([
                'name'       => $data['name'],
                'document'   => $data['document'],
                'birth_date' => $data['birth_date'],
                'gender'     => $data['gender'] ?? 'not_specified',
                'email'      => $data['email'],
                'phone'      => $data['phone'] ?? null,
                'address'    => $data['address'] ?? null,
            ]);

            $professional = Professional::create([
                'person_id'    => $person->id,
                'position_id'  => $data['position_id'],
                'registration' => $data['registration'],
                'entry_date'   => $data['entry_date'],
                'status'       => $data['status'] ?? 'active',
            ]);

            User::create([
                'name'             => $person->name,
                'email'            => $person->email,
                'password'         => Hash::make('napne2026'),
                'role'             => 'professional',
                'professional_id'  => $professional->id,
            ]);

            return $professional;
        });
    }

    /**
     * Atualiza Pessoa + Profissional
     */
    public function update(
        Professional $professional,
        array $data
    ): Professional {
        return DB::transaction(function () use ($professional, $data) {

            $professional->person->update([
                'name'       => $data['name'],
                'document'   => $data['document'],
                'birth_date' => $data['birth_date'],
                'gender'     => $data['gender']
                    ?? $professional->person->gender,
                'email'      => $data['email'],
                'phone'      => $data['phone'] ?? null,
                'address'    => $data['address'] ?? null,
            ]);

            $professional->update([
                'position_id'  => $data['position_id'],
                'registration' => $data['registration'],
                'entry_date'   => $data['entry_date'],
                'status'       => $data['status']
                    ?? $professional->status,
            ]);

            return $professional;
        });
    }

    public function delete(Professional $professional): void
    {
        DB::transaction(function () use ($professional) {
            $professional->delete();
        });
    }
}
