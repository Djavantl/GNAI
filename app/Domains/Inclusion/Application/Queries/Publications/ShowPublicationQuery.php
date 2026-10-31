<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Publications;

use App\Domains\Inclusion\Domain\Models\Publication;

final class ShowPublicationQuery
{
    public function execute(Publication $publication): Publication
    {
        return $publication->load(['author', 'media', 'speakers.professional.person', 'speakers.professional.position', 'tags']);
    }
}
