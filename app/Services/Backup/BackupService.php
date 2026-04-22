<?php

namespace App\Services\Backup;

use App\Models\Backup\Backup;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Process\Process;
use Throwable;
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
            $existingFiles = $this->snapshotBackupFiles();
            $startedAt     = time();
            $exitCode      = Artisan::call('backup:run', ['--disable-notifications' => true]);
            $output        = trim(Artisan::output());

            if ($exitCode !== 0) {
                throw new Exception($output !== '' ? $output : 'Falha ao executar o comando de backup.');
            }

            $latestFile = $this->findFreshBackupZip($existingFiles, $startedAt);

            if ($latestFile) {
                $absolutePath = $this->disk->path($latestFile);
                $this->assertBackupArchiveIsValid($absolutePath);

                return Backup::create([
                    'file_name' => basename($latestFile),
                    'file_path' => $latestFile,
                    'size'      => $this->formatBytes($this->disk->size($latestFile)),
                    'status'    => 'success',
                    'user_id'   => Auth::id(),
                ]);
            }

            throw new Exception('Backup executado, mas nenhum novo arquivo ZIP válido foi encontrado após a execução.');

        } catch (Exception $e) {
            Log::error("BackupService@generate: " . $e->getMessage());
            throw $e;
        }
    }

    public function storeUploadedFile($file): Backup
    {
        try {
            $fileName = $file->getClientOriginalName();
            $this->assertBackupArchiveIsValid($file->getRealPath());
            $path     = $this->disk->putFileAs((string) config('backup.backup.name', 'GNAIbackups'), $file, $fileName);

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
        $zipPath = $this->resolveBackupZipPath($backup);

        $this->assertBackupArchiveIsValid($zipPath);
        set_time_limit(300);

        $workPath     = storage_path('framework' . DIRECTORY_SEPARATOR . 'backup-restore-' . time() . '-' . bin2hex(random_bytes(4)));
        $extractPath  = $workPath . DIRECTORY_SEPARATOR . 'extracted';
        $rollbackPath = $workPath . DIRECTORY_SEPARATOR . 'rollback-app';

        try {
            File::ensureDirectoryExists($extractPath);
            $this->extractBackupArchive($zipPath, $extractPath);

            $sqlFile = $this->findSqlFile($extractPath);
            if (!$sqlFile) {
                throw new Exception('O backup selecionado não contém arquivo SQL para restauração.');
            }

            $connection = $this->resolveRestoreConnection();
            $this->restoreDatabaseFromSql($sqlFile, $connection['config']);

            $sourceStorage = $this->findStorageDir($extractPath);
            $this->restoreStorageApp($sourceStorage, $rollbackPath);

            return true;

        } catch (Throwable $e) {
            Log::error("BackupService@restore — falha crítica: " . $e->getMessage());
            throw $e instanceof Exception ? $e : new Exception($e->getMessage(), 0, $e);
        } finally {
            $this->removeDirectory($workPath);
        }
    }

    private function snapshotBackupFiles(): array
    {
        $backupFolder = config('backup.backup.name');
        $files        = $this->disk->allFiles($backupFolder);

        return collect($files)
            ->filter(fn($file) => str_ends_with($file, '.zip'))
            ->mapWithKeys(fn($file) => [$file => $this->disk->lastModified($file)])
            ->all();
    }

    private function findFreshBackupZip(array $existingFiles, int $startedAt): ?string
    {
        $backupFolder = config('backup.backup.name');
        $files        = $this->disk->allFiles($backupFolder);

        return collect($files)
            ->filter(fn($file) => str_ends_with($file, '.zip'))
            ->filter(function ($file) use ($existingFiles, $startedAt) {
                $lastModified = $this->disk->lastModified($file);
                $previous     = $existingFiles[$file] ?? null;

                return $previous === null || $lastModified > $previous || $lastModified >= $startedAt;
            })
            ->sortByDesc(fn($file) => $this->disk->lastModified($file))
            ->first();
    }

    private function assertBackupArchiveIsValid(string $zipPath): void
    {
        $zip = new ZipArchive();

        if ($zip->open($zipPath) !== true) {
            throw new Exception('O arquivo de backup não é um ZIP válido ou está corrompido.');
        }

        $hasSql = false;

        for ($index = 0; $index < $zip->numFiles; $index++) {
            $entryName = $zip->getNameIndex($index);
            if (is_string($entryName) && str_ends_with(strtolower($entryName), '.sql')) {
                $hasSql = true;
                break;
            }
        }

        $zip->close();

        if (!$hasSql) {
            throw new Exception('O arquivo ZIP não contém dump SQL válido para restauração.');
        }
    }

    private function resolveBackupZipPath(Backup $backup): string
    {
        $fileName     = $backup->file_name;
        $relativePath = str_replace('\\', '/', $backup->file_path);

        foreach (['storage/app/private/', 'storage/app/', 'private/'] as $prefix) {
            if (str_starts_with($relativePath, $prefix)) {
                $relativePath = substr($relativePath, strlen($prefix));
                break;
            }
        }

        $relativeCandidates = array_values(array_unique(array_filter([
            ltrim($relativePath, '/'),
            config('backup.backup.name') . '/' . $fileName,
        ])));

        foreach ($relativeCandidates as $candidate) {
            if ($this->disk->exists($candidate)) {
                return $this->disk->path($candidate);
            }
        }

        $absoluteCandidates = [
            storage_path('app/private/' . config('backup.backup.name') . '/' . $fileName),
            storage_path('app/' . config('backup.backup.name') . '/' . $fileName),
        ];

        foreach ($absoluteCandidates as $candidate) {
            if (file_exists($candidate)) {
                return $candidate;
            }
        }

        Log::error("BackupService@restore — arquivo não encontrado: {$fileName}");
        throw new Exception("Arquivo físico não encontrado: {$fileName}");
    }

    private function extractBackupArchive(string $zipPath, string $extractPath): void
    {
        $zip = new ZipArchive();

        if ($zip->open($zipPath) !== true) {
            throw new Exception("Falha ao abrir o ZIP: {$zipPath}");
        }

        if (!$zip->extractTo($extractPath)) {
            $zip->close();
            throw new Exception('Falha ao extrair o conteúdo do backup.');
        }

        $zip->close();
    }

    private function resolveRestoreConnection(): array
    {
        $connectionName = collect(config('backup.backup.source.databases', []))
            ->filter(fn($name) => is_string($name) && $name !== '')
            ->first() ?: config('database.default');

        $dbConfig = config("database.connections.{$connectionName}");

        if (!$dbConfig) {
            throw new Exception("Configuração de conexão não encontrada para restauração: {$connectionName}");
        }

        if (!in_array($dbConfig['driver'] ?? null, ['mysql', 'mariadb'], true)) {
            throw new Exception("Driver de banco não suportado para restauração automatizada: {$dbConfig['driver']}");
        }

        return [
            'name' => $connectionName,
            'config' => $dbConfig,
        ];
    }

    private function restoreDatabaseFromSql(string $sqlFile, array $dbConfig): void
    {
        $mysqlBin = $this->resolveMysqlBinary();
        $optFile  = null;

        try {
            $arguments = [$mysqlBin];

            if ($this->isWindows()) {
                $optFile     = $this->writeMysqlOptionsFile($dbConfig);
                $arguments[] = '--defaults-extra-file=' . $optFile;
            }

            $arguments = array_merge($arguments, $this->buildMysqlConnectionArguments($dbConfig));
            $arguments = array_merge($arguments, $this->buildMysqlRestoreExtraArguments($dbConfig));
            $arguments[] = $dbConfig['database'];

            $environment = null;
            if (!$this->isWindows() && isset($dbConfig['password'])) {
                $environment = ['MYSQL_PWD' => (string) $dbConfig['password']];
            }

            $process = new Process($arguments, base_path(), $environment);
            $process->setTimeout(300);
            $process->setInput(fopen($sqlFile, 'r'));
            $process->run();

            if (!$process->isSuccessful()) {
                $errorOutput = trim($process->getErrorOutput() ?: $process->getOutput());
                throw new Exception('Erro ao importar SQL: ' . ($errorOutput !== '' ? $errorOutput : 'processo retornou falha sem mensagem.'));
            }
        } finally {
            if ($optFile && file_exists($optFile)) {
                @unlink($optFile);
            }
        }
    }

    private function buildMysqlConnectionArguments(array $dbConfig): array
    {
        $arguments = [];

        if (!empty($dbConfig['unix_socket'])) {
            $arguments[] = '--socket=' . $dbConfig['unix_socket'];
        } else {
            $arguments[] = '--host=' . ($dbConfig['host'] ?? '127.0.0.1');
            $arguments[] = '--port=' . ($dbConfig['port'] ?? '3306');
        }

        if (!$this->isWindows()) {
            $arguments[] = '--user=' . ($dbConfig['username'] ?? '');
        }

        if (!empty($dbConfig['charset'])) {
            $arguments[] = '--default-character-set=' . $dbConfig['charset'];
        }

        return $arguments;
    }

    private function buildMysqlRestoreExtraArguments(array $dbConfig): array
    {
        $rawOptions = $dbConfig['dump']['add_extra_option'] ?? [];
        $options    = is_array($rawOptions)
            ? $rawOptions
            : (preg_split('/\s+/', trim((string) $rawOptions)) ?: []);

        return collect($options)
            ->filter(fn($option) => is_string($option) && $option !== '')
            ->filter(function (string $option) {
                return str_starts_with($option, '--protocol=')
                    || str_starts_with($option, '--ssl-mode=')
                    || $option === '--skip-ssl';
            })
            ->values()
            ->all();
    }

    private function restoreStorageApp(?string $sourceStorage, string $rollbackPath): void
    {
        $destination      = storage_path('app');
        $stagingPath      = dirname($rollbackPath) . DIRECTORY_SEPARATOR . 'staging-app';
        $backupFolderName = (string) config('backup.backup.name', 'GNAIbackups');
        $preservedBackups = $destination . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . $backupFolderName;

        File::ensureDirectoryExists($stagingPath);

        if ($sourceStorage && is_dir($sourceStorage)) {
            File::copyDirectory($sourceStorage, $stagingPath);
        }

        if (is_dir($preservedBackups)) {
            File::ensureDirectoryExists($stagingPath . DIRECTORY_SEPARATOR . 'private');
            File::copyDirectory(
                $preservedBackups,
                $stagingPath . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . $backupFolderName
            );
        }

        if (is_dir($destination)) {
            if (!File::moveDirectory($destination, $rollbackPath)) {
                throw new Exception('Falha ao preparar a troca do storage atual durante a restauração.');
            }
        }

        try {
            if (!File::moveDirectory($stagingPath, $destination)) {
                throw new Exception('Falha ao aplicar os arquivos restaurados no storage.');
            }

            $this->removeDirectory($rollbackPath);
        } catch (Throwable $e) {
            if (is_dir($rollbackPath) && !is_dir($destination)) {
                File::moveDirectory($rollbackPath, $destination);
            }

            throw $e instanceof Exception ? $e : new Exception($e->getMessage(), 0, $e);
        }
    }

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
        $content = "[client]\n";
        $content .= "user=\"{$dbConfig['username']}\"\n";
        $content .= "password=\"{$dbConfig['password']}\"\n";
        if (!empty($dbConfig['unix_socket'])) {
            $content .= "socket=\"{$dbConfig['unix_socket']}\"\n";
        } else {
            $content .= "host=\"{$dbConfig['host']}\"\n";
            $content .= "port=\"{$dbConfig['port']}\"\n";
        }
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
