<?php

namespace App\Services\InclusiveRadar;

use App\Models\AuditLog;
use App\Models\InclusiveRadar\Training;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TrainingService
{
    public function store(array $data): Training
    {
        return DB::transaction(fn() => $this->persist(new Training(), $data));
    }

    public function update(Training $training, array $data): Training
    {
        return DB::transaction(fn() => $this->persist($training, $data));
    }

    public function toggleActive(Training $training): Training
    {
        $training->update(['is_active' => !$training->is_active]);
        return $training;
    }

    public function delete(Training $training): void
    {
        DB::transaction(function () use ($training) {
            foreach ($training->files as $file) {
                if (Storage::disk('public')->exists($file->path)) {
                    Storage::disk('public')->delete($file->path);
                }
                $file->delete();
            }

            $training->delete();
        });
    }

    protected function persist(Training $training, array $data): Training
    {
        // 1. Limpeza de Links (URLs)
        if (isset($data['url']) && is_array($data['url'])) {
            $data['url'] = array_values(array_filter($data['url'], fn($value) => !is_null($value) && trim($value) !== ''));
        }

        // 2. Salva dados básicos + polimórficos
        $training->fill($data)->save();

        // 3. Upload de arquivos
        if (isset($data['files']) && is_array($data['files'])) {
            if (!Storage::disk('public')->exists('trainings')) {
                Storage::disk('public')->makeDirectory('trainings');
            }

            foreach ($data['files'] as $uploadedFile) {
                $path = $uploadedFile->store('trainings', 'public');

                $training->files()->create([
                    'path' => $path,
                    'original_name' => $uploadedFile->getClientOriginalName(),
                    'mime_type' => $uploadedFile->getClientMimeType(),
                    'size' => $uploadedFile->getSize(),
                ]);
            }
        }

        return $training->fresh(['trainable', 'files']);
    }
}
