<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Person;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfessionalService
{
    public function index(array $filters = [])
    {
        return Professional::query()
            ->select('professionals.*') 
            ->join('people', 'people.id', '=', 'professionals.person_id')
            ->with('person')
            ->globalSearch($filters['q'] ?? null)
            ->orderBy('people.name', 'asc') 
            ->paginate(10)
            ->withQueryString();
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
            // 1. Processa a foto
            if (isset($data['photo']) && $data['photo'] instanceof \Illuminate\Http\UploadedFile) {
                $data['photo'] = $data['photo']->store('photos', 'public');
            }
            $data['entry_date'] = now()->format('Y-m-d'); 

            // 2. Cria a Pessoa vinculando a foto
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

            // 3. Cria o Profissional
            $professional = Professional::create([
                'person_id'    => $person->id,
                'position_id'  => $data['position_id'],
                'registration' => $data['registration'],
                'entry_date'   => $data['entry_date'],
                'status'       => 'active',
            ]);

            // 4. Cria o Usuário de acesso
            User::create([
                'name'             => $person->name,
                'email'            => $person->email,
                'password'         => Hash::make('napne2026'),
                'role'             => 'professional',
                'professional_id'  => $professional->id,
                'is_admin'         => false,
            ]);

            return $professional;
        });
    }

    /**
     * Atualiza Pessoa + Profissional
     */
    public function update(Professional $professional, array $data): Professional 
    {
        return DB::transaction(function () use ($professional, $data) {
            $person = $professional->person;

            // Lógica de substituição da foto
            if (isset($data['photo']) && $data['photo'] instanceof \Illuminate\Http\UploadedFile) {
                if ($person->photo) {
                    Storage::disk('public')->delete($person->photo);
                }
                $data['photo'] = $data['photo']->store('photos', 'public');
            } 
            // Lógica para remover a foto se houver um checkbox 'remove_photo'
            elseif (!empty($data['remove_photo'])) {
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

            $professional->update([
                'position_id'  => $data['position_id'],
                'registration' => $data['registration'],
                'status'       => $data['status'] ?? $professional->status,
            ]);

            return $professional;
        });
    }

    /**
     * Deleta Profissional e limpa arquivos
     */
    public function delete(Professional $professional): void
    {
        DB::transaction(function () use ($professional) {
            // Deleta a foto física antes de apagar o registro
            if ($professional->person && $professional->person->photo) {
                Storage::disk('public')->delete($professional->person->photo);
            }

            $professional->delete();
        });
    }
}
