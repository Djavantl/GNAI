<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Pendencies;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Application\Data\Pendencies\UpdatePendencyData;
use App\Domains\Inclusion\Domain\DTOs\Pendencies\UpdatePendencyDTO;
use App\Domains\Inclusion\Domain\Exceptions\InvalidPendency;
use App\Domains\Inclusion\Domain\Exceptions\InvalidProfessional;
use App\Domains\Inclusion\Domain\Models\Pendency;
use App\Domains\Inclusion\Domain\Models\Professional;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdatePendencyAction
{
    /**
     * @throws InvalidPendency
     * @throws InvalidProfessional
     * @throws ModelNotFoundException
     * @throws Throwable
     */
    public function execute(Pendency $pendency, UpdatePendencyData $data, User $actor): Pendency
    {
        return DB::transaction(function () use ($pendency, $data, $actor): Pendency {
            $lockedPendency = Pendency::query()
                ->lockForUpdate()
                ->findOrFail($pendency->getKey());
            $lockedPendency->ensureCanBeEditedBy($actor);

            $professional = Professional::query()
                ->with('person')
                ->findOrFail($data->assignedTo);

            $pendencyDTO = new UpdatePendencyDTO(
                title: $data->title,
                priority: $data->priority,
                description: $data->description,
                dueDate: $data->dueDate,
            );

            $lockedPendency->revise(
                assignedProfessional: $professional,
                data: $pendencyDTO,
            );

            $lockedPendency->save();

            return $lockedPendency->load(['creator.professional.person', 'assignedProfessional.person']);
        });
    }
}
