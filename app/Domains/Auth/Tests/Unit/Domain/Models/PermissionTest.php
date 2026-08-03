<?php

declare(strict_types=1);

namespace App\Domains\Auth\Tests\Unit\Domain\Models;

use App\Domains\Auth\Domain\DTOs\Permissions\PermissionDTO;
use App\Domains\Auth\Domain\Exceptions\InvalidPermission;
use App\Domains\Auth\Domain\Models\Permission;
use PHPUnit\Framework\TestCase;

final class PermissionTest extends TestCase
{
    public function test_it_registers_a_normalized_permission(): void
    {
        $permission = Permission::register(new PermissionDTO(
            name: '  Visualizar   alunos ',
            slug: ' STUDENT.VIEW ',
        ));

        self::assertSame('Visualizar alunos', $permission->name);
        self::assertSame('student.view', $permission->slug);
    }

    public function test_it_rejects_an_invalid_slug(): void
    {
        $this->expectException(InvalidPermission::class);

        Permission::register(new PermissionDTO(
            name: 'Visualizar alunos',
            slug: 'student',
        ));
    }

    public function test_slug_cannot_change_during_synchronization(): void
    {
        $permission = Permission::register(new PermissionDTO(
            name: 'Visualizar alunos',
            slug: 'student.view',
        ));

        $this->expectException(InvalidPermission::class);

        $permission->synchronize(new PermissionDTO(
            name: 'Editar alunos',
            slug: 'student.update',
        ));
    }
}
