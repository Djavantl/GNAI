<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Pendencies;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Application\Data\Pendencies\CreatePendencyData;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Pendencies\CreatePendencyDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidPendency;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidProfessional;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Pendency;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Professional;
use App\Domains\SpecializedEducationalSupport\Infrastructure\Notifications\NewPendencyNotification;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreatePendencyAction
{
    /**
     * @throws InvalidPendency
     * @throws InvalidProfessional
     * @throws ModelNotFoundException
     * @throws Throwable
     */
    public function execute(User $creator, CreatePendencyData $data): Pendency
    {
        $pendency = DB::transaction(function () use ($creator, $data): Pendency {
            $lockedProfessional = Professional::query()
                ->with('person')
                ->lockForUpdate()
                ->findOrFail($data->assignedTo);

            $pendencyDTO = new CreatePendencyDTO(
                title: $data->title,
                priority: $data->priority,
                description: $data->description,
                dueDate: $data->dueDate,
            );

            $pendency = Pendency::register(
                creator: $creator,
                assignedProfessional: $lockedProfessional,
                data: $pendencyDTO,
            );

            $pendency->save();

            return $pendency->load(['creator.professional.person', 'assignedProfessional.user']);
        });

        $pendency->assignedProfessional?->user?->notify(new NewPendencyNotification($pendency));

        return $pendency;
    }
}
