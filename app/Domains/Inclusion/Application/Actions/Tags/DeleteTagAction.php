<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Tags;

use App\Domains\Inclusion\Domain\Models\Tag;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteTagAction
{
    /**
     * @throws Throwable
     */
    public function execute(Tag $tag): void
    {
        DB::transaction(function () use ($tag): void {
            Tag::query()
                ->findOrFail($tag->getKey())
                ->delete();
        });
    }
}
