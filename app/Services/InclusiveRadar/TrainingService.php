<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\Training;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class TrainingService
{
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

    public function delete(Training $training): void
    {
        DB::transaction(function () use ($training) {
            /* Garantimos que a remoção do registro limpe também o armazenamento físico
               para evitar o acúmulo de arquivos sem referência no servidor. */
            $this->deleteFiles($training);
            $training->delete();
        });
    }

    protected function persist(Training $training, array $data): Training
    {
        $this->validateTrainableIntegrity($training, $data);

        $data = $this->sanitizeUrls($data);

        $this->saveModel($training, $data);

        $this->handleUploads($training, $data);

        return $this->loadFreshRelations($training);
    }

    private function sanitizeUrls(array $data): array
    {
        if (isset($data['url']) && is_array($data['url'])) {
            /* Removemos entradas vazias ou nulas para manter a limpeza dos metadados
               e evitar erros de renderização em links na interface. */
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

        foreach ($data['files'] as $uploadedFile) {
            /* Estruturamos os uploads em pastas por ID do treinamento para facilitar
               a manutenção e exclusão em lote dos arquivos vinculados. */
            $path = $uploadedFile->store("trainings/{$training->id}", 'public');

            $training->files()->create([
                'path' => $path,
                'original_name' => $uploadedFile->getClientOriginalName(),
                'mime_type' => $uploadedFile->getClientMimeType(),
                'size' => $uploadedFile->getSize(),
            ]);
        }
    }

    private function deleteFiles(Training $training): void
    {
        foreach ($training->files as $file) {
            if (Storage::disk('public')->exists($file->path)) {
                Storage::disk('public')->delete($file->path);
            }
            $file->delete();
        }

        /* Além de deletar os arquivos individualmente, removemos o diretório raiz
           do treinamento para manter o sistema de arquivos organizado. */
        $directory = "trainings/{$training->id}";
        if (Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->deleteDirectory($directory);
        }
    }

    private function loadFreshRelations(Training $training): Training
    {
        return $training->fresh([
            'trainable',
            'files'
        ]);
    }

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

        /* Bloqueamos a troca de vínculo (trainable) após a criação para preservar
           a rastreabilidade pedagógica do material vinculado ao recurso original. */
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
