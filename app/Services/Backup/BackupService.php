<?php

namespace App\Services\Backup;

use App\Models\Backup\Backup;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class BackupService
{
    protected $disk;

    public function __construct()
    {
        $this->disk = Storage::disk('local');
    }

    public function generate(): Backup
    {
        try {
            Artisan::call('backup:run', ['--disable-notifications' => true]);

            $backupFolder = config('backup.backup.name');
            $files        = $this->disk->allFiles($backupFolder);

            $latestFile = collect($files)
                ->filter(fn($f) => str_ends_with($f, '.zip'))
                ->sortByDesc(fn($f) => $this->disk->lastModified($f))
                ->first();

            if ($latestFile) {
                return Backup::create([
                    'file_name' => basename($latestFile),
                    'file_path' => $latestFile,
                    'size'      => $this->formatBytes($this->disk->size($latestFile)),
                    'status'    => 'success',
                    'user_id'   => Auth::id(),
                ]);
            }

            throw new Exception("Backup executado, mas o ZIP não foi encontrado em: {$backupFolder}");

        } catch (Exception $e) {
            Log::error("BackupService@generate: " . $e->getMessage());
            throw $e;
        }
    }

    public function storeUploadedFile($file): Backup
    {
        try {
            $fileName = $file->getClientOriginalName();
            $path     = $this->disk->putFileAs('GNAIbackups', $file, $fileName);

            return Backup::create([
                'file_name' => $fileName,
                'file_path' => $path,
                'size'      => $this->formatBytes($file->getSize()),
                'status'    => 'success',
                'user_id'   => Auth::id() ?? 1,
            ]);
        } catch (Exception $e) {
            Log::error("BackupService@storeUploadedFile: " . $e->getMessage());
            throw $e;
        }
    }

    public function delete($id): ?bool
    {
        $backup = Backup::findOrFail($id);

        if ($this->disk->exists($backup->file_path)) {
            $this->disk->delete($backup->file_path);
        }

        return $backup->delete();
    }

    public function sync(): bool
    {
        try {
            $backupFolder = config('backup.backup.name');
            $zipFiles     = array_filter(
                $this->disk->allFiles($backupFolder),
                fn($f) => str_ends_with($f, '.zip')
            );

            foreach ($zipFiles as $file) {
                $fileName = basename($file);
                if (!Backup::where('file_name', $fileName)->exists()) {
                    Backup::create([
                        'file_name' => $fileName,
                        'file_path' => $file,
                        'size'      => $this->formatBytes($this->disk->size($file)),
                        'status'    => 'success',
                        'user_id'   => Auth::id() ?? 1,
                    ]);
                }
            }

            foreach (Backup::all() as $dbBackup) {
                if (!$this->disk->exists($dbBackup->file_path)) {
                    $dbBackup->delete();
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error("BackupService@sync: " . $e->getMessage());
            return false;
        }
    }

    public function restore($id): bool
    {
        $backup = Backup::findOrFail($id);

        // ------------------------------------------------------------------
        // ETAPA 1: LOCALIZAÇÃO DO ARQUIVO — lê o caminho base do .env
        // O disco 'local' tem raiz em storage/app/private, então os paths
        // salvos no banco são relativos a essa raiz (ex: GNAIbackups/arq.zip)
        // ------------------------------------------------------------------
        $fileName   = $backup->file_name;
        $relativePath = str_replace('\\', '/', $backup->file_path);

        // Remove prefixos absolutos legados que possam ter vindo de outros ambientes
        $stripPrefixes = [
            'storage/app/private/',
            'storage/app/',
            'private/',
        ];
        foreach ($stripPrefixes as $prefix) {
            if (str_starts_with($relativePath, $prefix)) {
                $relativePath = substr($relativePath, strlen($prefix));
                break;
            }
        }

        // Monta candidatos de caminho absoluto sem assumir nenhum OS ou estrutura
        $storageRoot = storage_path('app/private');
        $candidates  = [
            $storageRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath),
            $storageRoot . DIRECTORY_SEPARATOR . 'GNAIbackups' . DIRECTORY_SEPARATOR . $fileName,
            storage_path('app' . DIRECTORY_SEPARATOR . 'GNAIbackups' . DIRECTORY_SEPARATOR . $fileName),
        ];

        $zipPath = null;
        foreach ($candidates as $candidate) {
            if (file_exists($candidate)) {
                $zipPath = $candidate;
                break;
            }
        }

        if (!$zipPath) {
            Log::error("BackupService@restore — arquivo não encontrado: {$fileName}");
            throw new Exception("Arquivo físico não encontrado: {$fileName}");
        }

        set_time_limit(300);

        $tempPath = storage_path('app' . DIRECTORY_SEPARATOR . 'restore-temp-' . time());
        $zip      = new ZipArchive();

        try {
            // ------------------------------------------------------------------
            // ETAPA 2: EXTRAÇÃO
            // ------------------------------------------------------------------
            if ($zip->open($zipPath) !== true) {
                throw new Exception("Falha ao abrir o ZIP: {$zipPath}");
            }
            $zip->extractTo($tempPath);
            $zip->close();

            // ------------------------------------------------------------------
            // ETAPA 3: RESTAURAÇÃO DO BANCO
            // Binário lido do .env via database.php — sem hardcode de OS
            // ------------------------------------------------------------------
            $sqlFile = $this->findSqlFile($tempPath);

            if ($sqlFile) {
                $dbConfig  = config('database.connections.mysql');
                $mysqlBin  = $this->resolveMysqlBinary();
                $optFile   = null;

                if ($this->isWindows()) {
                    // No Windows não existe VAR=value na frente do comando,
                    // então usamos um arquivo .cnf temporário com a senha
                    $optFile  = $this->writeMysqlOptionsFile($dbConfig);
                    $command  = sprintf(
                        '"%s" --defaults-extra-file=%s -h %s -P %s %s < %s 2>&1',
                        $mysqlBin,
                        escapeshellarg($optFile),
                        escapeshellarg($dbConfig['host']),
                        escapeshellarg($dbConfig['port']),
                        escapeshellarg($dbConfig['database']),
                        escapeshellarg($sqlFile)
                    );
                } else {
                    // Linux/Mac: injeta a senha via variável de ambiente
                    $command = sprintf(
                        'MYSQL_PWD=%s %s -h %s -P %s -u %s %s < %s 2>&1',
                        escapeshellarg($dbConfig['password']),
                        escapeshellarg($mysqlBin),
                        escapeshellarg($dbConfig['host']),
                        escapeshellarg($dbConfig['port']),
                        escapeshellarg($dbConfig['username']),
                        escapeshellarg($dbConfig['database']),
                        escapeshellarg($sqlFile)
                    );
                }

                exec($command, $output, $returnCode);

                if ($optFile && file_exists($optFile)) {
                    @unlink($optFile);
                }

                if ($returnCode !== 0) {
                    $error = collect($output)
                        ->reject(fn($l) => str_contains($l, 'Warning') || str_contains($l, 'Deprecated'))
                        ->first();

                    if ($error) {
                        Log::error("BackupService@restore — erro SQL: {$error}");
                        throw new Exception("Erro ao importar SQL: {$error}");
                    }
                }
            }

            // ------------------------------------------------------------------
            // ETAPA 4: RESTAURAÇÃO DE ARQUIVOS DE MÍDIA
            // Varre o ZIP extraído procurando a pasta storage/app
            // independente de qual era o path absoluto na máquina de origem
            // ------------------------------------------------------------------
            $sourceStorage = $this->findStorageDir($tempPath);

            if ($sourceStorage && is_dir($sourceStorage)) {
                $destination = storage_path('app');

                if ($this->isWindows()) {
                    exec('xcopy /E /I /Y /H '
                        . escapeshellarg($sourceStorage) . ' '
                        . escapeshellarg($destination));
                } else {
                    exec('cp -R ' . escapeshellarg($sourceStorage) . '/. '
                        . escapeshellarg($destination) . '/');
                }
            }

            return true;

        } catch (Exception $e) {
            Log::error("BackupService@restore — falha crítica: " . $e->getMessage());
            throw $e;
        } finally {
            // ------------------------------------------------------------------
            // ETAPA 5: GARBAGE COLLECTION
            // ------------------------------------------------------------------
            $this->removeDirectory($tempPath);
        }
    }

    // -----------------------------------------------------------------------
    // HELPERS PRIVADOS
    // -----------------------------------------------------------------------

    /**
     * Resolve o caminho absoluto do binário mysql.
     * Lê de database.connections.mysql.dump.dump_binary_path,
     * que por sua vez lê de BACKUP_MYSQL_BINARY_PATH no .env.
     * Fallback para 'mysql' no PATH do sistema.
     */
    private function resolveMysqlBinary(): string
    {
        $dir        = config('database.connections.mysql.dump.dump_binary_path', '');
        $binaryName = $this->isWindows() ? 'mysql.exe' : 'mysql';

        if ($dir) {
            $full = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . $binaryName;
            if (file_exists($full)) {
                return $full;
            }
        }

        // Fallback: depende do PATH global do sistema operacional
        return $binaryName;
    }

    /**
     * Cria arquivo temporário de opções MySQL (.cnf) para evitar
     * expor a senha como argumento de linha de comando no Windows.
     */
    private function writeMysqlOptionsFile(array $dbConfig): string
    {
        $path    = storage_path('app' . DIRECTORY_SEPARATOR . 'mysql-opts-' . time() . '.cnf');
        $content = "[client]\nuser={$dbConfig['username']}\npassword={$dbConfig['password']}\n";
        file_put_contents($path, $content);
        return $path;
    }

    /**
     * Localiza recursivamente o primeiro arquivo .sql na pasta extraída.
     */
    private function findSqlFile(string $directory): ?string
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory)
        );
        foreach ($iterator as $file) {
            if ($file->getExtension() === 'sql') {
                return $file->getRealPath();
            }
        }
        return null;
    }

    /**
     * Localiza a pasta "app" dentro de "storage" no conteúdo extraído do ZIP,
     * independente do path absoluto que tinha na máquina de origem.
     */
    private function findStorageDir(string $tempPath): ?string
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($tempPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $item) {
            if (
                $item->isDir()
                && $item->getFilename() === 'app'
                && basename(dirname($item->getRealPath())) === 'storage'
            ) {
                return $item->getRealPath();
            }
        }
        return null;
    }

    /**
     * Remove um diretório recursivamente, compatível com Windows e Linux.
     */
    private function removeDirectory(string $path): void
    {
        if (!is_dir($path)) return;

        if ($this->isWindows()) {
            exec('rd /s /q ' . escapeshellarg($path));
        } else {
            exec('rm -rf ' . escapeshellarg($path));
        }
    }

    private function isWindows(): bool
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    private function formatBytes(int|float $bytes, int $precision = 2): string
    {
        $units  = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes  = (float) max($bytes, 0);

        if ($bytes === 0.0) return '0 B';

        $power   = min((int) floor(log($bytes, 1024)), count($units) - 1);
        $rounded = round($bytes / (1024 ** $power), $precision);

        return $rounded . ' ' . $units[$power];
    }
}
