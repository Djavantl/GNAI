<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Application\Policies\Inspections;

use App\Domains\InclusiveRadar\Application\Data\Inspections\CreateInspectionData;
use App\Domains\InclusiveRadar\Application\Policies\Inspections\InspectionRegistrationPolicy;
use App\Domains\InclusiveRadar\Domain\Enums\InspectionType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class InspectionRegistrationPolicyTest extends TestCase
{
    /**
     * @param  array<int, mixed>  $images
     */
    #[DataProvider('registrationScenarios')]
    public function test_it_decides_when_an_inspection_must_be_registered(
        bool $stateChanged,
        ?string $description,
        array $images,
        bool $expected,
    ): void {
        $inspection = new CreateInspectionData(
            date: '2026-07-18',
            type: InspectionType::PERIODIC,
            description: $description,
            images: $images,
        );

        $policy = new InspectionRegistrationPolicy;

        self::assertSame(
            $expected,
            $policy->shouldRegister($stateChanged, $inspection),
        );
    }

    /**
     * @return iterable<string, array{bool, ?string, array<int, mixed>, bool}>
     */
    public static function registrationScenarios(): iterable
    {
        yield 'estado alterado' => [true, null, [], true];
        yield 'parecer informado' => [false, 'Recurso reavaliado.', [], true];
        yield 'imagem informada' => [false, null, ['imagem'], true];
        yield 'nenhuma mudança relevante' => [false, null, [], false];
        yield 'parecer vazio' => [false, '   ', [], false];
    }
}
