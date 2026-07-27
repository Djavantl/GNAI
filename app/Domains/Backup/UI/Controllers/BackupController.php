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
use App\Domains\Backup\Domain\Exceptions\BackupOperationFailed;
use App\Domains\Backup\Domain\Exceptions\InvalidBackup;
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

    /**
     * @throws BackupOperationFailed
     */
    public function store(GenerateBackupAction $action): RedirectResponse
    {
        $action->execute();

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

    /**
     * @throws BackupOperationFailed
     */
    public function upload(Request $request, StoreUploadedBackupAction $action): RedirectResponse
    {
        $data = UploadBackupData::validateAndCreate($request->all());

        if (! $data->backupFile->isValid()) {
            return redirect()->back()->with('error', 'Erro no upload do PHP: ' . $data->backupFile->getErrorMessage());
        }

        $action->execute($data);

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

    /**
     * @throws InvalidBackup
     * @throws BackupOperationFailed
     */
    public function restore(Backup $backup, RestoreBackupAction $action): RedirectResponse
    {
        $action->execute($backup);

        return redirect()
            ->route('backup.backups.index')
            ->with('success', 'Sistema restaurado com sucesso para a versão selecionada!');
    }
}
