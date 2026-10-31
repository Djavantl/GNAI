<?php

declare(strict_types=1);

namespace App\Domains\Auth\Application\Queries\Profiles;

use App\Domains\Auth\Domain\Models\User;

final readonly class EditAccountProfileQuery
{
    /**
     * @return array{account: array<string, mixed>}
     */
    public function execute(User $user): array
    {
        return [
            'account' => [
                'id' => $user->getKey(),
                'name' => $user->name,
                'email' => $user->email,
                'photo' => $user->photo,
                'photo_url' => $user->photo_url,
                'is_super_admin' => $user->isSuperAdmin(),
                'is_admin' => $user->isAdmin(),
            ],
        ];
    }
}
