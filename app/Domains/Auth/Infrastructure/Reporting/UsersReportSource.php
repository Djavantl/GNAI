<?php

declare(strict_types=1);

namespace App\Domains\Auth\Infrastructure\Reporting;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Reporting\Application\Services\ReportOptionCatalog;
use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Professional;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Teacher;

final class UsersReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'auth.users';
    }

    public function label(): string
    {
        return 'Usuários';
    }

    protected function model(): string
    {
        return User::class;
    }

    protected function with(): array
    {
        return ['professional.person', 'teacher.person'];
    }

    protected function definitions(): array
    {
        return [
            'name' => ['label' => 'Nome'], 'email' => ['label' => 'E-mail'],
            'role' => [
                'label' => 'Perfil',
                'type' => ReportColumnType::SELECT,
                'options' => ReportOptionCatalog::userRoles(),
            ],
            'is_admin' => ['label' => 'Administrador', 'type' => ReportColumnType::BOOLEAN],
            'email_verified_at' => ['label' => 'E-mail verificado em', 'type' => ReportColumnType::DATE],
            'created_at' => ['label' => 'Criado em', 'type' => ReportColumnType::DATE],
        ];
    }

    protected function filterable(): array
    {
        return ['name', 'email', 'role', 'is_admin', 'email_verified_at', 'created_at'];
    }

    protected function relationPolicy(string $parentModel, string $relationName): array
    {
        $excluded = in_array($parentModel, [Professional::class, Teacher::class], true)
            ? ['name', 'email']
            : [];

        return ['exclude' => $excluded];
    }
}
