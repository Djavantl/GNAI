<?php

declare(strict_types=1);

namespace App\Domains\Auth\Application\Queries\Users;

use App\Domains\Auth\Domain\Models\User;

class FindUserByIdQuery
{
    public function execute(int $id): ?User
    {
        return User::query()->find($id);
    }
}
