<?php

declare(strict_types=1);

namespace App\Domains\Auth\Application\Policies;

use App\Domains\Auth\Domain\Exceptions\InvalidAdministratorAccess;
use App\Domains\Auth\Domain\Models\User;

/**
 * Somente o administrador superior do Gnai concede ou retira a condição de
 * administrador interno. A regra não depende de permissões de cargos, por isso
 * não pode ser contornada com cargos personalizados.
 */
final readonly class AdministratorAccessPolicy
{
    /**
     * @throws InvalidAdministratorAccess
     */
    public function ensureCanAssign(?User $actor, ?User $target, bool $isAdmin): void
    {
        $this->ensureIsNotSuperAdmin($target);

        $current = $target?->isAdmin() === true;

        if ($current !== $isAdmin && ! $this->isSuperAdmin($actor)) {
            throw new InvalidAdministratorAccess(
                'Somente o administrador superior do Gnai pode conceder ou retirar o acesso de administrador.'
            );
        }
    }

    /**
     * Remoção ou desativação de uma conta administrativa.
     *
     * @throws InvalidAdministratorAccess
     */
    public function ensureCanRevokeAccess(?User $actor, ?User $target): void
    {
        $this->ensureIsNotSuperAdmin($target);

        if ($target?->isAdmin() === true && ! $this->isSuperAdmin($actor)) {
            throw new InvalidAdministratorAccess(
                'Somente o administrador superior do Gnai pode desativar ou remover um administrador.'
            );
        }
    }

    /**
     * O e-mail é o login da conta: alterá-lo permitiria assumir o acesso de outro administrador.
     *
     * @throws InvalidAdministratorAccess
     */
    public function ensureCanChangeEmail(?User $actor, ?User $target, string $email): void
    {
        $this->ensureIsNotSuperAdmin($target);

        if (
            $target?->isAdmin() === true
            && mb_strtolower((string) $target->email) !== mb_strtolower($email)
            && ! $this->isSuperAdmin($actor)
            && $actor?->getKey() !== $target->getKey()
        ) {
            throw new InvalidAdministratorAccess(
                'Somente o administrador superior do Gnai pode alterar o e-mail de acesso de outro administrador.'
            );
        }
    }

    private function isSuperAdmin(?User $actor): bool
    {
        return $actor?->isSuperAdmin() === true;
    }

    /**
     * @throws InvalidAdministratorAccess
     */
    private function ensureIsNotSuperAdmin(?User $target): void
    {
        if ($target?->isSuperAdmin() === true) {
            throw new InvalidAdministratorAccess(
                'A conta do administrador superior do Gnai é administrada somente pelo terminal do servidor.'
            );
        }
    }
}
