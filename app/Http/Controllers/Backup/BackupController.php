<?php

namespace App\Http\Controllers\Backup;

use App\Http\Controllers\Controller;
use App\Models\Backup\Backup;
use App\Models\User;
use App\Services\Backup\BackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BackupController extends Controller
{
    public function __construct(
        private BackupService $service
    ) {}

    public function index(Request $request): View
    {
        $this->service->sync();

        $backups = Backup::with('user')
            ->filterName(trim($request->name ?? '') ?: null)
            ->byType($request->status)
            ->byUser($request->user_id)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $users = User::whereHas('backups')->orderBy('name')->get();

        if ($request->ajax()) {
            return view('pages.backup.partials.table', compact('backups'));
        }

        return view('pages.backup.index', compact('backups', 'users'));
    }

    public function store(): RedirectResponse
    {
        $this->service->generate();

        return redirect()
            ->route('backup.backups.index')
            ->with('success', 'Backup realizado com sucesso!');
    }

    public function show($id): View
    {
        $backup = Backup::with('user')->findOrFail($id);

        return view('pages.backup.show', compact('backup'));
    }

    public function download($id): RedirectResponse|\Symfony\Component\HttpFoundation\StreamedResponse
    {
        $backup = Backup::findOrFail($id);

        if (Storage::disk('local')->exists($backup->file_path)) {
            return Storage::disk('local')->download($backup->file_path, $backup->file_name);
        }

        return redirect()->back()->with('error', 'O arquivo físico não existe no servidor.');
    }

    public function upload(Request $request): RedirectResponse
    {
        if (!$request->hasFile('backup_file')) {
            return redirect()->back()->with('error', 'O servidor não recebeu o arquivo. Verifique se o formulário tem enctype="multipart/form-data".');
        }

        $file = $request->file('backup_file');

        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'Erro no upload do PHP: ' . $file->getErrorMessage());
        }

        $request->validate(['backup_file' => 'required|file|max:102400']);

        $this->service->storeUploadedFile($file);

        return redirect()->back()->with('success', 'Backup importado com sucesso!');
    }

    public function destroy($id): RedirectResponse
    {
        $this->service->delete($id);

        return redirect()
            ->route('backup.backups.index')
            ->with('success', 'Registro e arquivo removidos permanentemente.');
    }

    public function restore($id): RedirectResponse
    {
        $this->service->restore($id);

        return redirect()
            ->route('backup.backups.index')
            ->with('success', 'Sistema restaurado com sucesso para a versão selecionada!');
    }
}
