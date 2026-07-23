<?php

declare(strict_types=1);

namespace App\Domains\Backup\UI\Controllers;

use App\Domains\Backup\Application\Actions\DeleteBackupAction;
use App\Domains\Backup\Application\Actions\GenerateBackupAction;
use App\Domains\Backup\Application\Actions\RestoreBackupAction;
use App\Domains\Backup\Application\Actions\StoreUploadedBackupAction;
use App\Domains\Backup\Application\Actions\SyncBackupsAction;
use App\Domains\Backup\Application\Data\ListBackupsData;
use App\Domains\Backup\Application\Data\UploadBackupData;
use App\Domains\Backup\Application\Queries\DownloadBackupQuery;
use App\Domains\Backup\Application\Queries\ListBackupsQuery;
use App\Domains\Backup\Application\Queries\ListBackupUsersQuery;
use App\Domains\Backup\Application\Queries\ShowBackupQuery;
use App\Domains\Backup\Domain\Models\Backup;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

final class BackupController extends Controller
{
    public function index(
        ListBackupsData $filters,
        ListBackupsQuery $backupsQuery,
        ListBackupUsersQuery $usersQuery,
        SyncBackupsAction $syncBackups,
        Request $request,
    ): View {
        $syncBackups->execute();

        $backups = $backupsQuery->execute($filters);
        $users = $usersQuery->execute();

        if ($request->ajax()) {
            return view('pages.backup.partials.table', compact('backups'));
        }

        return view('pages.backup.index', compact('backups', 'users'));
    }

    public function store(GenerateBackupAction $action): RedirectResponse
    {
        try {
            $action->execute();
        } catch (Throwable $exception) {
            return redirect()
                ->route('backup.backups.index')
                ->with('error', 'Falha ao gerar backup: ' . $exception->getMessage());
        }

        return redirect()
            ->route('backup.backups.index')
            ->with('success', 'Backup realizado com sucesso!');
    }

    public function show(Backup $backup, ShowBackupQuery $query): View
    {
        $backup = $query->execute($backup);

        return view('pages.backup.show', compact('backup'));
    }

    public function download(Backup $backup, DownloadBackupQuery $query): RedirectResponse|StreamedResponse
    {
        $response = $query->execute($backup);

        if ($response === null) {
            return redirect()->back()->with('error', 'O arquivo físico não existe no servidor.');
        }

        return $response;
    }

    public function upload(Request $request, StoreUploadedBackupAction $action): RedirectResponse
    {
        if (! $request->hasFile('backup_file')) {
            return redirect()->back()->with('error', 'O servidor não recebeu o arquivo. Verifique se o formulário tem enctype="multipart/form-data".');
        }

        $file = $request->file('backup_file');

        if (! $file->isValid()) {
            return redirect()->back()->with('error', 'Erro no upload do PHP: ' . $file->getErrorMessage());
        }

        $request->validate(UploadBackupData::rules(), UploadBackupData::messages());

        try {
            $action->execute(new UploadBackupData(backupFile: $file));
        } catch (Throwable $exception) {
            return redirect()->back()->with('error', 'Falha ao importar backup: ' . $exception->getMessage());
        }

        return redirect()->back()->with('success', 'Backup importado com sucesso!');
    }

    /**
     * @throws Throwable
     */
    public function destroy(Backup $backup, DeleteBackupAction $action): RedirectResponse
    {
        $action->execute($backup);

        return redirect()
            ->route('backup.backups.index')
            ->with('success', 'Registro e arquivo removidos permanentemente.');
    }

    public function restore(Backup $backup, RestoreBackupAction $action): RedirectResponse
    {
        try {
            $action->execute($backup);
        } catch (Throwable $exception) {
            return redirect()
                ->route('backup.backups.index')
                ->with('error', 'Falha ao restaurar backup: ' . $exception->getMessage());
        }

        return redirect()
            ->route('backup.backups.index')
            ->with('success', 'Sistema restaurado com sucesso para a versão selecionada!');
    }
}
