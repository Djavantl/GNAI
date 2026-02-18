<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\Training;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class TrainingService
{
    /*
    |--------------------------------------------------------------------------
    | CRUD
    |--------------------------------------------------------------------------
    */

    public function store(array $data): Training
    {
        return DB::transaction(
            fn() => $this->persist(new Training(), $data)
        );
    }

    public function update(Training $training, array $data): Training
    {
        return DB::transaction(
            fn() => $this->persist($training, $data)
        );
    }

    public function toggleActive(Training $training): Training
    {
        $training->update([
            'is_active' => !$training->is_active
        ]);

        return $training;
    }

    public function delete(Training $training): void
    {
        DB::transaction(function () use ($training) {

            $this->deleteFiles($training);

            $training->delete();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | PERSIST (Fluxo Principal)
    |--------------------------------------------------------------------------
    */

    protected function persist(Training $training, array $data): Training
    {
        $this->validateTrainableIntegrity($training, $data);

        $data = $this->sanitizeUrls($data);

        $this->saveModel($training, $data);

        $this->handleUploads($training, $data);

        return $this->loadFreshRelations($training);
    }

    /*
    |--------------------------------------------------------------------------
    | Etapas do Persist
    |--------------------------------------------------------------------------
    */

    private function sanitizeUrls(array $data): array
    {
        if (isset($data['url']) && is_array($data['url'])) {
            $data['url'] = array_values(
                array_filter(
                    $data['url'],
                    fn($value) => !is_null($value) && trim($value) !== ''
                )
            );
        }

        return $data;
    }

    private function saveModel(Training $training, array $data): void
    {
        $training->fill($data)->save();
    }

    private function handleUploads(Training $training, array $data): void
    {
        if (!isset($data['files']) || !is_array($data['files'])) {
            return;
        }

        $this->ensureDirectoryExists();

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

    private function ensureDirectoryExists(): void
    {
        if (!Storage::disk('public')->exists('trainings')) {
            Storage::disk('public')->makeDirectory('trainings');
        }
    }

    private function deleteFiles(Training $training): void
    {
        foreach ($training->files as $file) {
            $file->delete();
        }
    }

    private function loadFreshRelations(Training $training): Training
    {
        return $training->fresh([
            'trainable',
            'files'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Regras de Negócio
    |--------------------------------------------------------------------------
    */

    private function validateTrainableIntegrity(Training $training, array $data): void
    {
        if (!$training->exists) {
            return;
        }

        $newType = $data['trainable_type'] ?? null;
        $newId = isset($data['trainable_id']) ? (int) $data['trainable_id'] : null;

        if (!$newType || !$newId) {
            throw ValidationException::withMessages([
                'trainable_id' => 'O treinamento deve permanecer vinculado a uma entidade.'
            ]);
        }

        if (
            $training->trainable_type !== $newType ||
            (int)$training->trainable_id !== $newId
        ) {
            throw ValidationException::withMessages([
                'trainable_id' => 'Não é permitido alterar o vínculo deste treinamento.'
            ]);
        }
    }
}
