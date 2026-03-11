<?php

namespace App\Http\Controllers\Backup;

use App\Http\Controllers\Controller;
use App\Models\Backup\Backup;
use App\Models\User;
use App\Services\Backup\BackupService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BackupController extends Controller {

    public function __construct(
        protected BackupService $backupService
    ) {}

    public function index(Request $request, BackupService $backupService)
    {
        // 1. Sincroniza os arquivos da pasta storage/app/private/GNAIbackups com o Banco de Dados
        // Isso garante que backups colados manualmente apareçam na lista
        $backupService->sync();

        $name = trim($request->name ?? '');

        // Busca usuários que já realizaram backups para o filtro da lateral/topo
        $users = User::whereHas('backups')->orderBy('name')->get();

        // 2. Lista os backups (agora já sincronizados) com os filtros aplicados
        $backups = Backup::with('user')
            ->filterName($name ?: null)
            ->byType($request->status)
            ->byUser($request->user_id)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Resposta para requisições AJAX (filtros dinâmicos)
        if ($request->ajax()) {
            return view('pages.backup.partials.table', compact('backups'));
        }

        return view('pages.backup.index', compact('backups', 'users'));
    }

    public function store(Request $request) {
        try {
            $this->backupService->generate();
            return redirect()->route('backup.backups.index')->with('success', 'Backup realizado com sucesso!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao gerar backups: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $backup = Backup::with('user')->findOrFail($id);

        return view('pages.backup.show', compact('backup'));
    }

    public function download($id) {
        $backup = Backup::findOrFail($id);

        if (Storage::disk('local')->exists($backup->file_path)) {
            return Storage::disk('local')->download($backup->file_path, $backup->file_name);
        }

        return redirect()->back()->with('error', 'O arquivo físico não existe no servidor.');
    }

    public function upload(Request $request, BackupService $service)
    {

        try {
            // 1. Verifica se o arquivo sequer existe na requisição
            if (!$request->hasFile('backup_file')) {
                // Se cair aqui, o Nginx ou o PHP-FPM descartaram o arquivo antes do Laravel
                return redirect()->back()->with('error', 'O servidor não recebeu o arquivo. Verifique se o formulário tem enctype="multipart/form-data".');
            }

            $file = $request->file('backup_file');

            // 2. Verifica se houve erro no upload do PHP (Ex: erro de permissão temporária)
            if (!$file->isValid()) {
                return redirect()->back()->with('error', 'Erro no upload do PHP: ' . $file->getErrorMessage());
            }

            // 3. Validação do Laravel
            $request->validate([
                'backup_file' => 'required|file|max:102400',
            ]);

            // 4. Tenta salvar
            $service->storeUploadedFile($file);

            return redirect()->back()->with('success', 'Backup importado com sucesso!');

        } catch (ValidationException $e) {
            // Se cair aqui, o Laravel achou o arquivo mas ele não passou na regra 'file' ou 'max'
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (Exception $e) {
            Log::error("Erro Crítico no Upload NAI: " . $e->getMessage());
            return redirect()->back()->with('error', 'Falha no processamento: ' . $e->getMessage());
        }
    }

    public function destroy($id) {
        try {
            $this->backupService->delete($id);
            return redirect()->route('backup.backups.index')->with('success', 'Registro e arquivo removidos permanentemente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao excluir o backups.');
        }
    }

    public function restore($id)
    {
        try {
            $this->backupService->restore($id);

            return redirect()->route('backup.backups.index')
                ->with('success', 'Sistema restaurado com sucesso para a versão selecionada!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao restaurar backup: ' . $e->getMessage());
        }
    }
}
