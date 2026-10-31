<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Tags;

use App\Domains\Inclusion\Domain\Models\Tag;

final class ShowTagQuery
{
    public function execute(Tag $tag): Tag
    {
        return $tag->loadCount('publications');
    }
}
