<?php

declare(strict_types=1);

namespace App\Domains\Auth\Application\Queries\Profiles;

use App\Domains\Auth\Domain\Exceptions\InvalidProfile;
use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Person;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Professional;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Teacher;

final readonly class EditProfileQuery
{
    /**
     * @return array{person: Person, professional: ?Professional, teacher: ?Teacher}
     *
     * @throws InvalidProfile
     */
    public function execute(User $user): array
    {
        $this->ensureCanManageProfile($user);

        $user->loadMissing(['professional.person', 'professional.position', 'teacher.person']);

        $person = $user->professional?->person ?? $user->teacher?->person;

        if (! $person instanceof Person) {
            throw new InvalidProfile('Vínculo de pessoa não encontrado para este usuário.');
        }

        return [
            'person' => $person,
            'professional' => $user->professional,
            'teacher' => $user->teacher,
        ];
    }

    /**
     * @throws InvalidProfile
     */
    private function ensureCanManageProfile(User $user): void
    {
        if ($user->is_admin && ! $user->professional_id) {
            throw new InvalidProfile(
                'Administradores sem vínculo profissional não possuem perfil de dados pessoais para edição.'
            );
        }
    }
}
