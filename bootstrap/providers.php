<?php

use App\Domains\Auth\Infrastructure\Providers\AuthServiceProvider;
use App\Domains\Backup\Infrastructure\Providers\BackupServiceProvider;
use App\Domains\InclusiveRadar\Infrastructure\Providers\InclusiveRadarServiceProvider;
use App\Domains\Reporting\Infrastructure\Providers\ReportingServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    BackupServiceProvider::class,
    InclusiveRadarServiceProvider::class,
    ReportingServiceProvider::class,
];
