<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Domain\Models;

use App\Domains\InclusiveRadar\Domain\DTOs\Institutions\CreateInstitutionDTO;
use App\Domains\InclusiveRadar\Domain\DTOs\Institutions\UpdateInstitutionDTO;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidInstitution;
use App\Domains\InclusiveRadar\Domain\Models\Institution;
use Tests\TestCase;

final class InstitutionTest extends TestCase
{
    public function test_it_registers_an_institution(): void
    {
        $institution = Institution::register(new CreateInstitutionDTO(
            name: ' IFBA Campus Guanambi ',
            city: ' Guanambi ',
            state: ' Bahia ',
            latitude: -14.22,
            longitude: -42.77,
            shortName: ' IFBA-GBI ',
            district: ' Centro ',
            address: ' Rua A ',
            defaultZoom: 16,
            isActive: true,
        ));

        self::assertSame('IFBA Campus Guanambi', $institution->name);
        self::assertSame('IFBA-GBI', $institution->short_name);
        self::assertSame('Guanambi', $institution->city);
        self::assertSame('Bahia', $institution->state);
        self::assertSame('Centro', $institution->district);
        self::assertSame('Rua A', $institution->address);
        self::assertSame(16, $institution->default_zoom);
        self::assertTrue($institution->is_active);
    }

    public function test_it_revises_an_institution(): void
    {
        $institution = new Institution([
            'name' => 'Antiga',
            'city' => 'Guanambi',
            'state' => 'Bahia',
            'latitude' => -14.22,
            'longitude' => -42.77,
            'is_active' => true,
        ]);

        $institution->revise(new UpdateInstitutionDTO(
            name: 'Nova',
            city: 'Salvador',
            state: 'Bahia',
            latitude: -12.97,
            longitude: -38.50,
            shortName: '',
            district: '',
            address: '',
            defaultZoom: null,
            isActive: false,
        ));

        self::assertSame('Nova', $institution->name);
        self::assertSame('Salvador', $institution->city);
        self::assertNull($institution->short_name);
        self::assertSame(16, $institution->default_zoom);
        self::assertFalse($institution->is_active);
    }

    public function test_it_rejects_empty_name(): void
    {
        $this->expectException(InvalidInstitution::class);
        $this->expectExceptionMessage('O nome da instituição é obrigatório.');

        Institution::register(new CreateInstitutionDTO(
            name: ' ',
            city: 'Guanambi',
            state: 'Bahia',
            latitude: -14.22,
            longitude: -42.77,
        ));
    }

    public function test_it_rejects_invalid_latitude(): void
    {
        $this->expectException(InvalidInstitution::class);
        $this->expectExceptionMessage('A latitude deve estar entre -90 e 90.');

        Institution::register(new CreateInstitutionDTO(
            name: 'IFBA',
            city: 'Guanambi',
            state: 'Bahia',
            latitude: -100,
            longitude: -42.77,
        ));
    }

}
