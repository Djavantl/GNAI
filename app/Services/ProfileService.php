<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    /**
     * Atualiza os dados de perfil (Person e User).
     */
    public function updateProfile(User $user, array $data, $photo = null): void
    {
        // Identifica a Person vinculada
        $person = $user->professional?->person ?? $user->teacher?->person;

        if (!$person) {
            throw new \Exception("Vínculo de pessoa não encontrado para este usuário.");
        }

        // 1. Atualizar dados da Person
        $person->fill($data);

        
        // --- NOVA LÓGICA DE FOTO ---
        if ($photo) {
            // 1. Se enviou uma foto nova, deleta a antiga e salva a nova
            if ($person->photo) {
                Storage::disk('public')->delete($person->photo);
            }
            $person->photo = $photo->store('profile_photos', 'public');
        } 
        elseif (!empty($data['remove_photo'])) {
            // 2. Se marcou para remover a foto e não enviou uma nova
            if ($person->photo) {
                Storage::disk('public')->delete($person->photo);
            }
            $person->photo = null;
        }
        
        $person->save();

        // 3. Atualizar dados do User (Sincronização)
        $user->name = $data['name'];
        $user->email = $data['email'];

        // 4. Lógica da Senha
        if (!empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }

        $user->save();
    }
}