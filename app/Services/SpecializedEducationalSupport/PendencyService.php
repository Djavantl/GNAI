<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Pendency;
use App\Models\SpecializedEducationalSupport\Professional;
use Illuminate\Database\Eloquent\Collection;
use DomainException;
use Illuminate\Support\Facades\Auth;
use App\Enums\Priority;
use App\Notifications\NewPendencyNotification;
use App\Notifications\PendencyCompletedNotification;


class PendencyService
{
    public function index(array $filters = [])
    {
        return Pendency::query()
            ->with(['assignedProfessional.person', 'creator'])

            ->title($filters['title'] ?? null)
            ->assignedTo($filters['assigned_to'] ?? null)
            ->priority($filters['priority'] ?? null)
            ->completed($filters['is_completed'] ?? null)

            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    //criar
    public function create(array $data): Pendency
    {
        $this->ensureProfessionalIsActive($data['assigned_to']);

        $pendency = Pendency::create([
            'created_by'   => Auth::id(),
            'assigned_to'  => $data['assigned_to'],
            'title'        => $data['title'],
            'description'  => $data['description'] ?? null,
            'priority'     => $data['priority'],
            'due_date'     => $data['due_date'] ?? null,
            'is_completed' => false,
        ]);

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
    public function getMyPendencies(array $filters = [])
    {
        $professionalId = Auth::user()->professional->id;

        return Pendency::query()
            ->with(['assignedProfessional.person', 'creator'])
            ->where('assigned_to', $professionalId)

            ->title($filters['title'] ?? null)
            ->priority($filters['priority'] ?? null)
            ->completed($filters['is_completed'] ?? null)

            ->latest()
            ->paginate(10)
            ->withQueryString();
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
        if ($pendency->created_by !== Auth::id()) {
            throw new DomainException(
                'Somente o criador pode editar a pendência.'
            );
        }

        if ($pendency->is_completed) {
            throw new DomainException(
                'Não é possível editar uma pendência já concluída.'
            );
        }

        $professionalId = $data['assigned_to'] ?? $pendency->assigned_to;

        $this->ensureProfessionalIsActive($professionalId);

        $pendency->update([
            'assigned_to' => $data['assigned_to'] ?? $pendency->assigned_to,
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'priority'    => $data['priority'],
            'due_date'    => $data['due_date'] ?? null,

        ]);

        return $pendency;
    }

    //completar
    public function markAsCompleted(Pendency $pendency): Pendency
    {
        $professionalId = Auth::user()->professional->id;

        if ( $pendency->assigned_to !== $professionalId ) {
            throw new \DomainException(
                'Somente o responsavel pode concluir a pendência.'
            );
        }

        $pendency->markAsCompleted();

        // notificar quem criou
        if ($pendency->creator && $pendency->creator->user) {
            $pendency->creator->user->notify(
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

    private function ensureProfessionalIsActive(int $professionalId): void
    {
        $professional = Professional::with('person')
            ->findOrFail($professionalId);

        $professional->ensureIsActive();
    }
}
