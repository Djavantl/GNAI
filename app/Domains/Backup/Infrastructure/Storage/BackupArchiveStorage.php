<?php

declare(strict_types=1);

namespace App\Domains\Backup\Infrastructure\Storage;

use App\Domains\Backup\Application\Contracts\BackupArchiveStorageContract;
use App\Domains\Backup\Domain\Models\Backup;
use Exception;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Process\Process;
use Throwable;
use ZipArchive;

final class BackupArchiveStorage implements BackupArchiveStorageContract
{
    private const int RESTORE_TIMEOUT_SECONDS = 300;

    private readonly Filesystem $disk;

    public function __construct()
    {
        $this->disk = Storage::disk('local');
    }

    /**
     * @throws Exception
     */
    public function generateArchive(): BackupArchiveMetadata
    {
        $existingFiles = $this->snapshotBackupFiles();
        $startedAt = time();
        $exitCode = Artisan::call('backup:run', ['--disable-notifications' => true]);
        $output = trim(Artisan::output());

        if ($exitCode !== 0) {
            throw new Exception($output !== '' ? $output : 'Falha ao executar o comando de backup.');
        }

        $latestFile = $this->findFreshBackupZip($existingFiles, $startedAt);

        if ($latestFile === null) {
            throw new Exception('Backup executado, mas nenhum novo arquivo ZIP válido foi encontrado após a execução.');
        }

        $this->assertBackupArchiveIsValid($this->disk->path($latestFile));

        return new BackupArchiveMetadata(
            fileName: basename($latestFile),
            filePath: $latestFile,
            size: $this->formatBytes($this->disk->size($latestFile)),
        );
    }

    /**
     * @throws Exception
     */
    public function storeUploadedArchive(UploadedFile $file): BackupArchiveMetadata
    {
        $this->assertBackupArchiveIsValid((string) $file->getRealPath());

        $fileName = $this->generateUploadedArchiveFileName();
        $path = $this->disk->putFileAs($this->backupFolderName(), $file, $fileName);

        if ($path === false) {
            throw new Exception('Falha ao armazenar o arquivo de backup enviado.');
        }

        return new BackupArchiveMetadata(
            fileName: $fileName,
            filePath: $path,
            size: $this->formatBytes($file->getSize()),
        );
    }

    private function generateUploadedArchiveFileName(): string
    {
        return sprintf(
            'uploaded-backup-%s-%s.zip',
            now()->format('Ymd-His'),
            Str::random(12),
        );
    }

    public function deleteArchive(string $filePath): bool
    {
        if (! $this->disk->exists($filePath)) {
            return false;
        }

        return $this->disk->delete($filePath);
    }

    public function archiveExists(string $filePath): bool
    {
        return $this->disk->exists($filePath);
    }

    public function backupArchiveExists(Backup $backup): bool
    {
        try {
            $this->resolveBackupZipPath($backup);

            return true;
        } catch (Exception) {
            return false;
        }
    }

    /**
     * @return list<BackupArchiveMetadata>
     */
    public function listStoredArchives(): array
    {
        return collect($this->disk->allFiles($this->backupFolderName()))
            ->filter(fn (string $file): bool => str_ends_with($file, '.zip'))
            ->map(fn (string $file): BackupArchiveMetadata => new BackupArchiveMetadata(
                fileName: basename($file),
                filePath: $file,
                size: $this->formatBytes($this->disk->size($file)),
            ))
            ->values()
            ->all();
    }

    /**
     * @throws Exception|Throwable
     */
    public function restoreArchive(Backup $backup): void
    {
        $zipPath = $this->resolveBackupZipPath($backup);

        $this->assertBackupArchiveIsValid($zipPath);
        set_time_limit(self::RESTORE_TIMEOUT_SECONDS);

        $workPath = storage_path('framework'.DIRECTORY_SEPARATOR.'backup-restore-'.time().'-'.bin2hex(random_bytes(4)));
        $extractPath = $workPath.DIRECTORY_SEPARATOR.'extracted';
        $rollbackPath = $workPath.DIRECTORY_SEPARATOR.'rollback-app';
        $shouldCleanupWorkPath = true;

        try {
            File::ensureDirectoryExists($extractPath);
            $this->extractBackupArchive($zipPath, $extractPath);

            $sqlFile = $this->findSqlFile($extractPath);

            if ($sqlFile === null) {
                throw new Exception('O backup selecionado não contém arquivo SQL para restauração.');
            }

            $connection = $this->resolveRestoreConnection();
            $this->restoreDatabaseFromSql($sqlFile, $connection['config']);

            $sourceStorage = $this->findStorageDir($extractPath);
            $this->restoreStorageApp($sourceStorage, $rollbackPath, $shouldCleanupWorkPath);
        } catch (Throwable $exception) {
            Log::error('BackupArchiveStorage@restoreArchive — falha crítica: '.$exception->getMessage());

            throw $exception instanceof Exception
                ? $exception
                : new Exception($exception->getMessage(), 0, $exception);
        } finally {
            if ($shouldCleanupWorkPath) {
                $this->removeDirectory($workPath);
            }
        }
    }

    /**
     * @return array<string, int>
     */
    private function snapshotBackupFiles(): array
    {
        return collect($this->disk->allFiles($this->backupFolderName()))
            ->filter(fn (string $file): bool => str_ends_with($file, '.zip'))
            ->mapWithKeys(fn (string $file): array => [$file => $this->disk->lastModified($file)])
            ->all();
    }

    /**
     * @param  array<string, int>  $existingFiles
     */
    private function findFreshBackupZip(array $existingFiles, int $startedAt): ?string
    {
        return collect($this->disk->allFiles($this->backupFolderName()))
            ->filter(fn (string $file): bool => str_ends_with($file, '.zip'))
            ->filter(function (string $file) use ($existingFiles, $startedAt): bool {
                $lastModified = $this->disk->lastModified($file);
                $previous = $existingFiles[$file] ?? null;

                return $previous === null || $lastModified > $previous || $lastModified >= $startedAt;
            })
            ->sortByDesc(fn (string $file): int => $this->disk->lastModified($file))
            ->first();
    }

    /**
     * @throws Exception
     */
    private function assertBackupArchiveIsValid(string $zipPath): void
    {
        $zip = new ZipArchive;

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

        if (! $hasSql) {
            throw new Exception('O arquivo ZIP não contém dump SQL válido para restauração.');
        }
    }

    /**
     * @throws Exception
     */
    private function resolveBackupZipPath(Backup $backup): string
    {
        $fileName = $backup->file_name;
        $relativePath = str_replace('\\', '/', $backup->file_path);

        foreach (['storage/app/private/', 'storage/app/', 'private/'] as $prefix) {
            if (str_starts_with($relativePath, $prefix)) {
                $relativePath = substr($relativePath, strlen($prefix));
                break;
            }
        }

        $relativeCandidates = array_values(array_unique(array_filter([
            ltrim($relativePath, '/'),
            $this->backupFolderName().'/'.$fileName,
        ])));

        foreach ($relativeCandidates as $candidate) {
            if ($this->disk->exists($candidate)) {
                return $this->disk->path($candidate);
            }
        }

        $absoluteCandidates = [
            storage_path('app/private/'.$this->backupFolderName().'/'.$fileName),
            storage_path('app/'.$this->backupFolderName().'/'.$fileName),
        ];

        foreach ($absoluteCandidates as $candidate) {
            if (file_exists($candidate)) {
                return $candidate;
            }
        }

        Log::error("BackupArchiveStorage@restoreArchive — arquivo não encontrado: {$fileName}");

        throw new Exception("Arquivo físico não encontrado: {$fileName}");
    }

    /**
     * @throws Exception
     */
    private function extractBackupArchive(string $zipPath, string $extractPath): void
    {
        $zip = new ZipArchive;

        if ($zip->open($zipPath) !== true) {
            throw new Exception("Falha ao abrir o ZIP: {$zipPath}");
        }

        if (! $zip->extractTo($extractPath)) {
            $zip->close();
            throw new Exception('Falha ao extrair o conteúdo do backup.');
        }

        $zip->close();
    }

    /**
     * @return array{name: string, config: array<string, mixed>}
     *
     * @throws Exception
     */
    private function resolveRestoreConnection(): array
    {
        $connectionName = collect(config('backup.backup.source.databases', []))
            ->filter(fn (mixed $name): bool => is_string($name) && $name !== '')
            ->first() ?: config('database.default');

        $dbConfig = config("database.connections.{$connectionName}");

        if (! $dbConfig) {
            throw new Exception("Configuração de conexão não encontrada para restauração: {$connectionName}");
        }

        if (! in_array($dbConfig['driver'] ?? null, ['mysql', 'mariadb'], true)) {
            throw new Exception("Driver de banco não suportado para restauração automatizada: {$dbConfig['driver']}");
        }

        return [
            'name' => $connectionName,
            'config' => $dbConfig,
        ];
    }

    /**
     * @param  array<string, mixed>  $dbConfig
     *
     * @throws Exception
     */
    private function restoreDatabaseFromSql(string $sqlFile, array $dbConfig): void
    {
        $mysqlBin = $this->resolveMysqlBinary();
        $optFile = null;

        try {
            $arguments = [$mysqlBin];

            if ($this->isWindows()) {
                $optFile = $this->writeMysqlOptionsFile($dbConfig);
                $arguments[] = '--defaults-extra-file='.$optFile;
            }

            $arguments = array_merge($arguments, $this->buildMysqlConnectionArguments($dbConfig));
            $arguments = array_merge($arguments, $this->buildMysqlRestoreExtraArguments($dbConfig));
            $arguments[] = $dbConfig['database'];

            $environment = null;

            if (! $this->isWindows() && isset($dbConfig['password'])) {
                $environment = ['MYSQL_PWD' => (string) $dbConfig['password']];
            }

            $process = new Process($arguments, base_path(), $environment);
            $process->setTimeout(self::RESTORE_TIMEOUT_SECONDS);
            $process->setInput(fopen($sqlFile, 'r'));
            $process->run();

            if (! $process->isSuccessful()) {
                $errorOutput = trim($process->getErrorOutput() ?: $process->getOutput());

                throw new Exception('Erro ao importar SQL: '.($errorOutput !== '' ? $errorOutput : 'processo retornou falha sem mensagem.'));
            }
        } finally {
            if ($optFile !== null && file_exists($optFile)) {
                @unlink($optFile);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $dbConfig
     * @return list<string>
     */
    private function buildMysqlConnectionArguments(array $dbConfig): array
    {
        $arguments = [];

        if (! empty($dbConfig['unix_socket'])) {
            $arguments[] = '--socket='.$dbConfig['unix_socket'];
        } else {
            $arguments[] = '--host='.($dbConfig['host'] ?? '127.0.0.1');
            $arguments[] = '--port='.($dbConfig['port'] ?? '3306');
        }

        if (! $this->isWindows()) {
            $arguments[] = '--user='.($dbConfig['username'] ?? '');
        }

        if (! empty($dbConfig['charset'])) {
            $arguments[] = '--default-character-set='.$dbConfig['charset'];
        }

        return $arguments;
    }

    /**
     * @param  array<string, mixed>  $dbConfig
     * @return list<string>
     */
    private function buildMysqlRestoreExtraArguments(array $dbConfig): array
    {
        $rawOptions = $dbConfig['dump']['add_extra_option'] ?? [];
        $options = is_array($rawOptions)
            ? $rawOptions
            : (preg_split('/\s+/', trim((string) $rawOptions)) ?: []);

        return collect($options)
            ->filter(fn (mixed $option): bool => is_string($option) && $option !== '')
            ->filter(function (string $option): bool {
                return str_starts_with($option, '--protocol=')
                    || str_starts_with($option, '--ssl-mode=')
                    || $option === '--skip-ssl';
            })
            ->values()
            ->all();
    }

    /**
     * @throws Exception|Throwable
     */
    private function restoreStorageApp(?string $sourceStorage, string $rollbackPath, bool &$shouldCleanupWorkPath): void
    {
        $destination = storage_path('app');
        $stagingPath = dirname($rollbackPath).DIRECTORY_SEPARATOR.'staging-app';
        $backupFolderName = $this->backupFolderName();
        $preservedBackups = $destination.DIRECTORY_SEPARATOR.'private'.DIRECTORY_SEPARATOR.$backupFolderName;

        File::ensureDirectoryExists($stagingPath);

        if ($sourceStorage !== null && is_dir($sourceStorage)) {
            File::copyDirectory($sourceStorage, $stagingPath);
        }

        if (is_dir($preservedBackups)) {
            File::ensureDirectoryExists($stagingPath.DIRECTORY_SEPARATOR.'private');
            File::copyDirectory(
                $preservedBackups,
                $stagingPath.DIRECTORY_SEPARATOR.'private'.DIRECTORY_SEPARATOR.$backupFolderName,
            );
        }

        if (is_dir($destination) && ! File::moveDirectory($destination, $rollbackPath)) {
            throw new Exception('Falha ao preparar a troca do storage atual durante a restauração.');
        }

        try {
            if (! File::moveDirectory($stagingPath, $destination)) {
                throw new Exception('Falha ao aplicar os arquivos restaurados no storage.');
            }

            $this->removeDirectory($rollbackPath);
        } catch (Throwable $exception) {
            if (is_dir($rollbackPath)) {
                if (is_dir($destination)) {
                    $shouldCleanupWorkPath = false;

                    Log::critical('Restauração falhou e o rollback não pôde ser aplicado porque o destino já existe.', [
                        'rollback_path' => $rollbackPath,
                        'destination' => $destination,
                    ]);

                    throw new Exception(
                        "Falha crítica: rollback não pôde ser aplicado porque o destino já existe. Dados originais preservados em {$rollbackPath}",
                        0,
                        $exception,
                    );
                }

                if (! File::moveDirectory($rollbackPath, $destination)) {
                    $shouldCleanupWorkPath = false;

                    Log::critical('Restauração falhou e o rollback do storage original também falhou.', [
                        'rollback_path' => $rollbackPath,
                        'destination' => $destination,
                    ]);

                    throw new Exception(
                        "Falha crítica: rollback não pôde ser aplicado. Dados originais preservados em {$rollbackPath}",
                        0,
                        $exception,
                    );
                }
            }

            throw $exception instanceof Exception
                ? $exception
                : new Exception($exception->getMessage(), 0, $exception);
        }
    }

    private function resolveMysqlBinary(): string
    {
        $dir = config('database.connections.mysql.dump.dump_binary_path', '');
        $binaryName = $this->isWindows() ? 'mysql.exe' : 'mysql';

        if ($dir) {
            $full = rtrim($dir, '/\\').DIRECTORY_SEPARATOR.$binaryName;

            if (file_exists($full)) {
                return $full;
            }
        }

        return $binaryName;
    }

    /**
     * @param  array<string, mixed>  $dbConfig
     */
    private function writeMysqlOptionsFile(array $dbConfig): string
    {
        $path = storage_path('app'.DIRECTORY_SEPARATOR.'mysql-opts-'.time().'.cnf');
        $content = "[client]\n";
        $content .= "user=\"{$dbConfig['username']}\"\n";
        $content .= "password=\"{$dbConfig['password']}\"\n";

        if (! empty($dbConfig['unix_socket'])) {
            $content .= "socket=\"{$dbConfig['unix_socket']}\"\n";
        } else {
            $content .= "host=\"{$dbConfig['host']}\"\n";
            $content .= "port=\"{$dbConfig['port']}\"\n";
        }

        file_put_contents($path, $content);

        return $path;
    }

    private function findSqlFile(string $directory): ?string
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory),
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() === 'sql') {
                return $file->getRealPath();
            }
        }

        return null;
    }

    private function findStorageDir(string $tempPath): ?string
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($tempPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST,
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

    private function removeDirectory(string $path): void
    {
        if (! is_dir($path)) {
            return;
        }

        if ($this->isWindows()) {
            exec('rd /s /q '.escapeshellarg($path));

            return;
        }

        exec('rm -rf '.escapeshellarg($path));
    }

    private function isWindows(): bool
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    private function backupFolderName(): string
    {
        return (string) config('backup.backup.name', 'GNAIbackups');
    }

    private function formatBytes(int|float $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = (float) max($bytes, 0);

        if ($bytes === 0.0) {
            return '0 B';
        }

        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);
        $rounded = round($bytes / (1024 ** $power), $precision);

        return $rounded.' '.$units[$power];
    }
}
