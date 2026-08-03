<?php

declare(strict_types=1);

namespace App\Domains\Auth\UI\Controllers;

use App\Domains\Auth\Application\Actions\Profiles\UpdateProfileAction;
use App\Domains\Auth\Application\Data\Profiles\UpdateProfileData;
use App\Domains\Auth\Application\Queries\Profiles\EditProfileQuery;
use App\Domains\Auth\Application\Resolvers\AuthenticatedUserResolver;
use App\Domains\Auth\Domain\Exceptions\InvalidProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

final class ProfileController
{
    public function edit(
        EditProfileQuery $query,
        AuthenticatedUserResolver $authenticatedUser,
        Request $request,
    ): View|RedirectResponse {
        try {
            return view(
                'pages.profile.edit',
                $query->execute($authenticatedUser->fromRequest($request)),
            );
        } catch (InvalidProfile $exception) {
            return redirect()->route('dashboard')
                ->with('error', $exception->getMessage());
        }
    }

    /**
     * @throws Throwable
     */
    public function update(
        UpdateProfileData $data,
        UpdateProfileAction $action,
        AuthenticatedUserResolver $authenticatedUser,
        Request $request,
    ): RedirectResponse {
        $action->execute($authenticatedUser->fromRequest($request), $data);

        return back()->with('success', 'Seu perfil foi atualizado com sucesso!');
    }
}
