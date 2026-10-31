<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\InstitutionalEvents;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Domain\Enums\InstitutionalEventType;
use App\Domains\Inclusion\Domain\Models\InstitutionalEvent;
use App\Domains\Inclusion\Domain\Enums\ProfessionalStatus;
use App\Domains\Inclusion\Domain\Models\Professional;

/**
 * Opções do formulário de publicações: tipos, autores, palestrantes e
 * categorias já utilizadas (sugestões, sem cadastro próprio).
 */
final class InstitutionalEventFormQuery
{
    /**
     * @param  list<int>  $selectedSpeakerIds  palestrantes já vinculados, mesmo inativos
     * @return array<string, mixed>
     */
    public function execute(?User $currentUser, array $selectedSpeakerIds = []): array
    {
        $authors = User::query()
            ->with(['professional.person', 'teacher.person'])
            ->get()
            ->map(fn (User $user): array => [
                'value' => (string) $user->getKey(),
                'label' => (string) $user->name,
            ])
            ->sortBy('label', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();

        $speakers = Professional::query()
            ->with(['person', 'position'])
            ->where(function ($query) use ($selectedSpeakerIds): void {
                $query->where('status', ProfessionalStatus::ACTIVE->value)
                    ->orWhereIn('id', $selectedSpeakerIds);
            })
            ->get()
            ->map(fn (Professional $professional): array => [
                'value' => (string) $professional->getKey(),
                'label' => trim(($professional->person?->name ?? 'Profissional')
                    .($professional->position?->name ? ' — '.$professional->position->name : '')),
            ])
            ->sortBy('label', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();

        $categories = InstitutionalEvent::query()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->all();

        $types = [];

        foreach (InstitutionalEventType::options() as $value => $label) {
            $types[] = ['value' => $value, 'label' => $label];
        }

        return [
            'types' => $types,
            'authors' => $authors,
            'speakers' => $speakers,
            'categories' => $categories,
            'defaultAuthorId' => $currentUser?->getKey(),
            'publicBaseUrl' => url('/site/publicacoes'),
        ];
    }
}
