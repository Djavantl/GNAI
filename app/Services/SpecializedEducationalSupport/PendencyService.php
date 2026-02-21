<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Pendency;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use App\Enums\Priority;
use App\Notifications\NewPendencyNotification;
use App\Notifications\PendencyCompletedNotification;

class PendencyService
{
    //criar
    public function create(array $data): Pendency
    {
        $pendency = Pendency::create([
            'created_by'   => Auth::id(),
            'assigned_to'  => $data['assigned_to'],
            'title'        => $data['title'],
            'description'  => $data['description'] ?? null,
            'priority'     => $data['priority'],
            'due_date'     => $data['due_date'] ?? null,
            'is_completed' => false,
        ]);

        // ----- notificar o profissional/usuário associado -----
        // assumindo que assignedProfessional->user existe
        $assignedProfessional = $pendency->assignedProfessional;
        if ($assignedProfessional && $assignedProfessional->user) {
            $user = $assignedProfessional->user;
            $user->notify(new NewPendencyNotification($pendency));
        }

        return $pendency;
    }

    //pegar todas
    public function getAll(): Collection
    {
        return Pendency::with(['creator', 'assignedProfessional'])
            ->orderBy('due_date')
            ->get();
    }

    //ver 
    public function findById(int $id): Pendency
    {
        return Pendency::with(['creator', 'assignedProfessional'])
            ->findOrFail($id);
    }

    //pegar de um profissional
    public function getByProfessional(int $professionalId): Collection
    {
        return Pendency::with('creator')
            ->where('assigned_to', $professionalId)
            ->orderBy('is_completed')
            ->orderBy('due_date')
            ->get();
    }

    //pegar as propias
    public function getMyPendencies(): Collection
    {
        $professionalId = Auth::user()->professional->id;

        return $this->getByProfessional($professionalId);
    }

    //pegar pendentes
    public function getPending(): Collection
    {
        return Pendency::pending()
            ->with(['creator', 'assignedProfessional'])
            ->orderBy('due_date')
            ->get();
    }

    //pegar completas
    public function getCompleted(): Collection
    {
        return Pendency::completed()
            ->with(['creator', 'assignedProfessional'])
            ->orderByDesc('updated_at')
            ->get();
    }

    //atualizar
    public function update(Pendency $pendency, array $data): Pendency
    {
        $pendency->update([
            'assigned_to'  => $data['assigned_to'],
            'title'        => $data['title'],
            'description'  => $data['description'] ?? null,
            'priority'     => $data['priority'],
            'due_date'     => $data['due_date'] ?? null,
            'is_completed' => $data['is_completed'] ?? $pendency->is_completed,
        ]);

        return $pendency;
    }

    //completar
    public function markAsCompleted(Pendency $pendency): Pendency
    {
        $pendency->markAsCompleted();

        // notificar quem criou
        if ($pendency->creator) {
            $pendency->creator->notify(
                new PendencyCompletedNotification($pendency)
            );
        }

        return $pendency;
    }

    //delete
    public function delete(Pendency $pendency): void
    {
        $pendency->delete();
    }
}
