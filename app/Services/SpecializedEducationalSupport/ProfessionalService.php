<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Person;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use DomainException;
use App\Models\SpecializedEducationalSupport\Session;
use App\Models\SpecializedEducationalSupport\Pendency;
use App\Models\SpecializedEducationalSupport\Position;

class ProfessionalService
{
    public function index(array $filters = [])
    {
        return Professional::query()
            ->select('professionals.*')
            ->join('people', 'people.id', '=', 'professionals.person_id')
            ->with(['person', 'position'])
        
            ->name($filters['name'] ?? null)
            ->email($filters['email'] ?? null)
            ->position($filters['position'] ?? null)
            ->semester($filters['semester'] ?? null)
            ->status($filters['status'] ?? null)

            ->orderBy('people.name')
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
            $this->ensurePositionIsActive($data['position_id']);
            
            // 1. Processa a foto
            if (isset($data['photo']) && $data['photo'] instanceof \Illuminate\Http\UploadedFile) {
                $data['photo'] = $data['photo']->store('photos', 'public');
            }
            $data['entry_date'] = now()->format('Y-m-d'); 

            // 2. Cria a Pessoa vinculando a foto
            $person = Person::create([
                'name'       => $data['name'],
                'document'   => $data['document'] ?? null,
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
            $isAdmin = false;

            if (auth()->check() && auth()->user()->isAdmin()) {
                $isAdmin = (bool) ($data['is_admin'] ?? false);
            }

            User::create([
                'name'             => $person->name,
                'email'            => $person->email,
                'password'         => Hash::make('napne2026'),
                'role'             => 'professional',
                'professional_id'  => $professional->id,
                'is_admin'         => $isAdmin,
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
            $this->ensurePositionIsActive($data['position_id']);

            $person = $professional->person;

            if (isset($data['photo']) && $data['photo'] instanceof \Illuminate\Http\UploadedFile) {
                if ($person->photo) {
                    Storage::disk('public')->delete($person->photo);
                }

                $data['photo'] = $data['photo']->store('photos', 'public');
            } 
            elseif (!empty($data['remove_photo'])) {
                if ($person->photo) {
                    Storage::disk('public')->delete($person->photo);
                }

                $data['photo'] = null;
            } 
            else {
                $data['photo'] = $person->photo;
            }

            $person->update([
                'name'       => $data['name'],
                'document'   => $data['document'] ?? null,
                'birth_date' => $data['birth_date'],
                'gender'     => $data['gender'] ?? $person->gender,
                'email'      => $data['email'],
                'phone'      => $data['phone'] ?? null,
                'address'    => $data['address'] ?? null,
                'photo'      => $data['photo'],
            ]);

            $statusAntigo = $professional->status;
            $statusNovo   = $data['status'] ?? $professional->status;

            if ($statusAntigo !== $statusNovo) {

                if ($statusNovo === 'inactive') {

                    $this->ensureCanBeInactivated($professional);

                }

            }

            $professional->update([
                'position_id'  => $data['position_id'],
                'registration' => $data['registration'],
                'status'       => $statusNovo,
            ]);

            $user = $professional->user;

            if ($user) {
                $userUpdate = [
                    'name'  => $person->name,
                    'email' => $person->email,
                ];

                if (auth()->check() && auth()->user()->isAdmin()) {
                    $userUpdate['is_admin'] = (bool) ($data['is_admin'] ?? false);
                }

                $user->update($userUpdate);
            }

            return $professional;
        });
    }

    /**
     * Deleta Profissional e limpa arquivos
     */
    public function delete(Professional $professional): void
    {
        if (auth()->check() && auth()->user()->professional_id === $professional->id) {
            throw new DomainException("Você não pode excluir seu próprio registro de profissional.");
        }

        DB::transaction(function () use ($professional) {
            // Deleta a foto física antes de apagar o registro
            if ($professional->person && $professional->person->photo) {
                Storage::disk('public')->delete($professional->person->photo);
            }

            $professional->delete();
        });
    }

    private function ensureCanBeInactivated(Professional $professional): void
    {
        $hasPendingPendencies = Pendency::where('assigned_to', $professional->id)
            ->where('is_completed', false)
            ->exists();

        if ($hasPendingPendencies) {
            throw new DomainException(
                "O profissional {$professional->person->name} possui pendências em aberto e não pode ser inativado."
            );
        }

        $hasActiveSessions = Session::where('professional_id', $professional->id)
            ->exists();

        if ($hasActiveSessions) {
            throw new DomainException(
                "O profissional {$professional->person->name} possui sessões registradas e não pode ser inativado."
            );
        }
    }

    private function ensurePositionIsActive(int $positionId): void
    {
        $position = Position::findOrFail($positionId);
        $position->ensureIsActive();
    }
}
