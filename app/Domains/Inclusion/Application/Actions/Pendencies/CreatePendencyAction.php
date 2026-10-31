<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Pendencies;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Application\Data\Pendencies\CreatePendencyData;
use App\Domains\Inclusion\Domain\DTOs\Pendencies\CreatePendencyDTO;
use App\Domains\Inclusion\Domain\Exceptions\InvalidPendency;
use App\Domains\Inclusion\Domain\Exceptions\InvalidProfessional;
use App\Domains\Inclusion\Domain\Models\Pendency;
use App\Domains\Inclusion\Domain\Models\Professional;
use App\Domains\Inclusion\Infrastructure\Notifications\NewPendencyNotification;
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
            $professional = Professional::query()
                ->with('person')
                ->findOrFail($data->assignedTo);

            $pendencyDTO = new CreatePendencyDTO(
                title: $data->title,
                priority: $data->priority,
                description: $data->description,
                dueDate: $data->dueDate,
            );

            $pendency = Pendency::register(
                creator: $creator,
                assignedProfessional: $professional,
                data: $pendencyDTO,
            );

            $pendency->save();

            return $pendency->load(['creator.professional.person', 'assignedProfessional.user']);
        });

        $pendency->assignedProfessional?->user?->notify(new NewPendencyNotification($pendency));

        return $pendency;
    }
}
