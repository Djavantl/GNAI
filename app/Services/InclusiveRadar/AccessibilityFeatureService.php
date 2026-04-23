<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\AccessibilityFeature;
use Illuminate\Support\Facades\DB;

class AccessibilityFeatureService
{
    /**
     * RF: cria um recurso de acessibilidade com persistência transacional.
     * Uso: fluxo de cadastro no CRUD administrativo do radar inclusivo.
     */
    public function store(array $data): AccessibilityFeature
    {
        return DB::transaction(function () use ($data) {
            return AccessibilityFeature::create($data);
        });
    }

    /**
     * RF: atualiza um recurso de acessibilidade preservando consistência transacional.
     * Uso: edição de cadastros usados por materiais e relatórios do módulo.
     */
    public function update(AccessibilityFeature $feature, array $data): AccessibilityFeature
    {
        return DB::transaction(function () use ($feature, $data) {
            $feature->update($data);
            return $feature->fresh();
        });
    }

    /**
     * RF: remove o recurso de acessibilidade dentro de transação única.
     * Uso: exclusão administrativa de itens não mais utilizados pelo radar.
     */
    public function delete(AccessibilityFeature $feature): void
    {
        DB::transaction(function () use ($feature) {
            $feature->delete();
        });
    }
}
