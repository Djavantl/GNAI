<?php

namespace App\Http\Controllers\Backup;

use App\Http\Controllers\Controller;
use App\Models\Backup\Backup;
use App\Services\Backup\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller {
    protected $backupService;

    public function __construct(BackupService $backupService) {
        $this->backupService = $backupService;
    }

    public function index() {
        $backups = Backup::with('user')->latest()->paginate(10);
        return view('backup.index', compact('backups'));
    }

    public function store(Request $request) {
        try {
            $this->backupService->generate();
            return redirect()->route('backup.backups.index')->with('success', 'Backup realizado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao gerar backups: ' . $e->getMessage());
        }
    }

    public function edit($id) {
        $backup = Backup::findOrFail($id);
        return view('backup.edit', compact('backup'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'file_name' => 'required|string|max:255',
            'status'    => 'required|string'
        ]);

        $backup = Backup::findOrFail($id);
        $backup->update([
            'file_name' => $request->file_name,
            'status'    => $request->status,
        ]);

        return redirect()->route('backup.backups.index')->with('success', 'Informações do backups atualizadas!');
    }

    public function download($id) {
        $backup = Backup::findOrFail($id);

        if (Storage::disk('local')->exists($backup->file_path)) {
            return Storage::disk('local')->download($backup->file_path, $backup->file_name);
        }

        return redirect()->back()->with('error', 'O arquivo físico não existe no servidor.');
    }

    public function destroy($id) {
        try {
            $this->backupService->delete($id);
            return redirect()->route('backup.backups.index')->with('success', 'Registro e arquivo removidos permanentemente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao excluir o backups.');
        }
    }
}
