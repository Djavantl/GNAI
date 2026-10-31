<?php

declare(strict_types=1);

namespace App\Domains\Settings\UI\Controllers;

use App\Domains\Settings\Application\Actions\SendTestMailAction;
use App\Domains\Settings\Application\Actions\UpdateBackupSettingsAction;
use App\Domains\Settings\Application\Actions\UpdateMailSettingsAction;
use App\Domains\Settings\Application\Data\SendTestMailData;
use App\Domains\Settings\Application\Data\UpdateBackupSettingsData;
use App\Domains\Settings\Application\Data\UpdateMailSettingsData;
use App\Domains\Settings\Application\Queries\SettingsFormQuery;
use App\Domains\Settings\Domain\Exceptions\MailTestFailed;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

final class SettingsController
{
    public function index(): RedirectResponse
    {
        return redirect()->route('settings.mail.edit');
    }

    public function editMail(SettingsFormQuery $form, Request $request): InertiaResponse
    {
        return Inertia::render('Settings/Mail', [
            ...$form->forMail(),
            'canUpdate' => $request->user()?->can('setting.update') === true,
        ]);
    }

    public function updateMail(UpdateMailSettingsData $data, UpdateMailSettingsAction $action): RedirectResponse
    {
        $action->execute($data);

        return redirect()
            ->route('settings.mail.edit')
            ->with('success', 'Configurações de e-mail salvas com sucesso. Os próximos envios usarão estes dados.');
    }

    /**
     * @throws MailTestFailed
     */
    public function sendTestMail(SendTestMailData $data, SendTestMailAction $action): RedirectResponse
    {
        $result = $action->execute($data);

        $message = $result->loggedOnly
            ? 'Mensagem de teste registrada no log da aplicação. O tipo de envio “Registro em log” não entrega e-mails reais.'
            : "E-mail de teste enviado para {$data->recipient}. Confira a caixa de entrada e a pasta de spam.";

        if (! $result->usedStoredSettings) {
            $message .= ' Como ainda não há configuração cadastrada, foram usados os valores do arquivo .env.';
        }

        return redirect()
            ->route('settings.mail.edit')
            ->with('success', $message);
    }

    public function editBackup(SettingsFormQuery $form, Request $request): InertiaResponse
    {
        return Inertia::render('Settings/Backup', [
            ...$form->forBackup(),
            'canUpdate' => $request->user()?->can('setting.update') === true,
        ]);
    }

    public function updateBackup(UpdateBackupSettingsData $data, UpdateBackupSettingsAction $action): RedirectResponse
    {
        $action->execute($data);

        return redirect()
            ->route('settings.backup.edit')
            ->with('success', 'Configurações de backup salvas com sucesso.');
    }
}
