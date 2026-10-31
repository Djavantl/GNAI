<?php

declare(strict_types=1);

namespace App\Domains\Settings\Infrastructure\Providers;

use App\Domains\Settings\Infrastructure\Mail\MailSettingsConfigurator;
use Illuminate\Support\ServiceProvider;

final class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // As configurações de e-mail são lidas apenas quando o envio é preparado,
        // sem consultar o banco a cada requisição ou comando.
        $this->app->afterResolving('mail.manager', function (): void {
            $this->app->make(MailSettingsConfigurator::class)->apply();
        });
    }
}
