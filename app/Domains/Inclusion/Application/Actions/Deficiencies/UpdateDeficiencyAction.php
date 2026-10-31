<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Deficiencies;

use App\Domains\Inclusion\Application\Data\Deficiencies\UpdateDeficiencyData;
use App\Domains\Inclusion\Domain\DTOs\Deficiencies\UpdateDeficiencyDTO;
use App\Domains\Inclusion\Domain\Models\Deficiency;

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
