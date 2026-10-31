<?php

declare(strict_types=1);

namespace App\Domains\Auth\UI\Controllers;

use App\Domains\Auth\Application\Actions\Profiles\UpdateAccountProfileAction;
use App\Domains\Auth\Application\Actions\Profiles\UpdateProfileAction;
use App\Domains\Auth\Application\Data\Profiles\UpdateAccountProfileData;
use App\Domains\Auth\Application\Data\Profiles\UpdateProfileData;
use App\Domains\Auth\Application\Queries\Profiles\EditAccountProfileQuery;
use App\Domains\Auth\Application\Queries\Profiles\EditProfileQuery;
use App\Domains\Auth\Application\Resolvers\AuthenticatedUserResolver;
use App\Domains\Auth\Domain\Exceptions\InvalidProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class ProfileController
{
    public function edit(
        EditProfileQuery $query,
        EditAccountProfileQuery $accountQuery,
        AuthenticatedUserResolver $authenticatedUser,
        Request $request,
    ): InertiaResponse|RedirectResponse {
        $user = $authenticatedUser->fromRequest($request);

        // Contas sem vínculo com a equipe (ex.: administrador superior) têm apenas o perfil de conta.
        if (! $user->hasPersonProfile()) {
            return Inertia::render('Profile/Account', $accountQuery->execute($user));
        }

        try {
            return Inertia::render(
                'Profile/Edit',
                $query->execute($user),
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

    /**
     * @throws Throwable
     */
    public function updateAccount(
        UpdateAccountProfileData $data,
        UpdateAccountProfileAction $action,
        AuthenticatedUserResolver $authenticatedUser,
        Request $request,
    ): RedirectResponse {
        $action->execute($authenticatedUser->fromRequest($request), $data);

        return back()->with('success', 'Seu perfil foi atualizado com sucesso!');
    }
}
