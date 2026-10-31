<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Tags;

use App\Domains\Inclusion\Application\Data\Tags\UpdateTagData;
use App\Domains\Inclusion\Domain\DTOs\Tags\UpdateTagDTO;
use App\Domains\Inclusion\Domain\Exceptions\InvalidTag;
use App\Domains\Inclusion\Domain\Models\Tag;

final readonly class UpdateTagAction
{
    /**
     * @throws InvalidTag
     */
    public function execute(Tag $tag, UpdateTagData $data): Tag
    {
        $tagDTO = new UpdateTagDTO(
            name: $data->name,
            isActive: $data->isActive,
        );

        $tag->revise($tagDTO);
        $tag->save();

        return $tag;
    }
}
