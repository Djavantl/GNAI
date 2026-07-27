<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Deficiencies;

use App\Domains\SpecializedEducationalSupport\Application\Data\Deficiencies\UpdateDeficiencyData;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Deficiencies\UpdateDeficiencyDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Deficiency;

final readonly class UpdateDeficiencyAction
{
    public function execute(Deficiency $deficiency, UpdateDeficiencyData $data): Deficiency
    {
        $deficiencyDTO = new UpdateDeficiencyDTO(
            name: $data->name,
            cidCode: $data->cidCode,
            description: $data->description,
        );

        $deficiency->revise($deficiencyDTO);
        $deficiency->save();

        return $deficiency;
    }
}
