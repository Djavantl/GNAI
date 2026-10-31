<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Tags;

use App\Domains\Inclusion\Application\Data\Tags\CreateTagData;
use App\Domains\Inclusion\Domain\DTOs\Tags\CreateTagDTO;
use App\Domains\Inclusion\Domain\Exceptions\InvalidTag;
use App\Domains\Inclusion\Domain\Models\Tag;

final readonly class CreateTagAction
{
    /**
     * @throws InvalidTag
     */
    public function execute(CreateTagData $data): Tag
    {
        $tagDTO = new CreateTagDTO(
            name: $data->name,
            isActive: $data->isActive,
        );

        $tag = Tag::register($tagDTO);
        $tag->save();

        return $tag;
    }
}
