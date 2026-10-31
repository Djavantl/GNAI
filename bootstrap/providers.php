<?php

use App\Domains\Audit\Infrastructure\Providers\AuditServiceProvider;
use App\Domains\Auth\Infrastructure\Providers\AuthServiceProvider;
use App\Domains\Backup\Infrastructure\Providers\BackupServiceProvider;
use App\Domains\Inclusion\Infrastructure\Providers\InclusionServiceProvider;
use App\Domains\Reporting\Infrastructure\Providers\ReportingServiceProvider;
use App\Domains\Settings\Infrastructure\Providers\SettingsServiceProvider;
use App\Domains\Website\Infrastructure\Providers\WebsiteServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    AuditServiceProvider::class,
    AuthServiceProvider::class,
    BackupServiceProvider::class,
    InclusionServiceProvider::class,
    ReportingServiceProvider::class,
    SettingsServiceProvider::class,
    WebsiteServiceProvider::class,
];
