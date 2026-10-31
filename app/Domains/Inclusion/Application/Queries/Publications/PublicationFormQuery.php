<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Publications;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Domain\Enums\ProfessionalStatus;
use App\Domains\Inclusion\Domain\Enums\PublicationType;
use App\Domains\Inclusion\Domain\Models\Professional;
use App\Domains\Inclusion\Domain\Models\Tag;

/**
 * Opções do formulário de publicações: tipos, autores, palestrantes e
 * etiquetas do cadastro de etiquetas.
 */
final class PublicationFormQuery
{
    /**
     * @param  list<int>  $selectedSpeakerIds  palestrantes já vinculados, mesmo inativos
     * @param  list<int>  $selectedTagIds  etiquetas já vinculadas, mesmo inativas
     * @return array<string, mixed>
     */
    public function execute(?User $currentUser, array $selectedSpeakerIds = [], array $selectedTagIds = []): array
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
                    .($professional->position?->name ? ', '.$professional->position->name : '')),
            ])
            ->sortBy('label', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();

        $tags = Tag::query()
            ->where(function ($query) use ($selectedTagIds): void {
                $query->where('is_active', true)
                    ->orWhereIn('id', $selectedTagIds);
            })
            ->orderBy('name')
            ->get()
            ->map(fn (Tag $tag): array => [
                'value' => (string) $tag->getKey(),
                'label' => (string) $tag->name,
            ])
            ->values()
            ->all();

        return [
            'types' => PublicationType::options(),
            'authors' => $authors,
            'speakers' => $speakers,
            'tags' => $tags,
            'defaultAuthorId' => $currentUser?->getKey(),
            'publicBaseUrl' => url('/site/publicacoes'),
        ];
    }
}
