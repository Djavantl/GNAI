<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Application\Queries;

final readonly class DashboardMetricsQuery
{
    public function __construct(
        private SpecializedEducationalSupportDashboardQuery $specializedEducationalSupport,
        private InclusiveRadarDashboardQuery $inclusiveRadar,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        return [
            'specializedEducationalSupport' => $this->specializedEducationalSupport->execute(),
            'inclusiveRadar' => $this->inclusiveRadar->execute(),
        ];
    }
}
