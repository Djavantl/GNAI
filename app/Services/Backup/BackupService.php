<?php

namespace App\Services\Backup;

use App\Models\Backup\Backup;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BackupService {
    protected $disk;

    public function __construct() {
        $this->disk = Storage::disk('local');
    }

    public function generate() {
        try {
            Artisan::call('backup:run', [
                '--disable-notifications' => true
            ]);

            $files = $this->disk->allFiles('GNAI');

            $latestFile = collect($files)
                ->filter(fn($file) => str_ends_with($file, '.zip'))
                ->sortByDesc(fn($file) => $this->disk->lastModified($file))
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

            throw new \Exception("Comando executado, mas o arquivo ZIP não foi encontrado na pasta GNAI.");

        } catch (\Exception $e) {
            Log::error("Erro no BackupService: " . $e->getMessage());
            throw $e;
        }
    }

    public function delete($id) {
        $backup = Backup::findOrFail($id);

        if ($this->disk->exists($backup->file_path)) {
            $this->disk->delete($backup->file_path);
        }

        return $backup->delete();
    }

    private function formatBytes(int|float $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = (float) max($bytes, 0);

        if ($bytes === 0.0) {
            return '0 B';
        }

        $power = (int) floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);

        $value = (float) ($bytes / (1024 ** $power));
        $rounded = (float) round($value, $precision);

        return $rounded . ' ' . $units[$power];
    }
}
