<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Disciplines;

use App\Domains\SpecializedEducationalSupport\Application\Data\Disciplines\CreateDisciplineData;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Disciplines\CreateDisciplineDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Discipline;

final readonly class CreateDisciplineAction
{
    public function execute(CreateDisciplineData $data): Discipline
    {
        $disciplineDTO = new CreateDisciplineDTO(
            name: $data->name,
            description: $data->description,
            isActive: $data->isActive,
        );

        $discipline = Discipline::register($disciplineDTO);
        $discipline->save();

        return $discipline;
    }
}
