<?php

namespace App\Services\Report\Modules;

use Illuminate\Support\Collection;

interface ReportModuleInterface
{
    public function getKey(): string;

    public function getTitle(): string;

    public function getData(array $filters): Collection;
}

