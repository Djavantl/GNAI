<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Pendencies;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Pendency;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Professional;
use App\Enums\Priority;
use Illuminate\Database\Eloquent\Collection;

final class PendencyFormQuery
{
    /**
     * @return array<string, mixed>
     */
    public function forCreation(): array
    {
        return [
            'professionals' => $this->professionals(),
            'priorities' => $this->priorities(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function forUpdate(Pendency $pendency): array
    {
        return [
            'pendency' => $pendency->loadMissing(['assignedProfessional.person', 'creator.professional.person']),
            'professionals' => $this->professionals(),
            'priorities' => $this->priorities(),
        ];
    }

    /**
     * @return Collection<int, Professional>
     */
    private function professionals(): Collection
    {
        return Professional::query()
            ->with('person')
            ->orderBy('id')
            ->get();
    }

    /**
     * @return array<string, string>
     */
    private function priorities(): array
    {
        return Priority::options();
    }
}
