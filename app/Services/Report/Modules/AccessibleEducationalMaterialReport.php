<?php

namespace App\Services\Report\Modules;

use App\Enums\InclusiveRadar\ConservationState;
use App\Domains\InclusiveRadar\Domain\Models\AccessibilityFeature;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Models\InclusiveRadar\ResourceType;
use Illuminate\Http\Request;

class AccessibleEducationalMaterialReport
{
    public function generate(Request $request)
    {
        $query = AccessibleEducationalMaterial::query()
            ->with(['type', 'resourceStatus', 'deficiencies', 'accessibilityFeatures']);

        $query = $this->applyFilters($query, $request);

        return $query->get();
    }

    protected function applyFilters($query, Request $request)
    {
        // 🔎 Nome (busca)
        if ($request->filled('mat_name')) {
            $query->where('name', 'like', '%' . $request->mat_name . '%');
        }

        // 🧩 Tipo/Categoria
        if ($request->filled('mat_type_id')) {
            $query->where('type_id', $request->mat_type_id);
        }

        // 📊 Status
        if ($request->filled('mat_status_id')) {
            $query->where('status_id', $request->mat_status_id);
        }

        // 🔘 Ativo/Inativo
        if ($request->has('mat_is_active')) {
            $query->where('is_active', $request->boolean('mat_is_active'));
        }

        // 🧱 Estado de conservação
        if ($request->filled('mat_conservation_state')) {
            $state = ConservationState::tryFrom($request->mat_conservation_state);
            if ($state) {
                $query->where('conservation_state', $state);
            }
        }

        // 🎓 Requer treinamento (novo filtro)
        if ($request->boolean('mat_requires_training')) {
            $query->where('requires_training', true);
        }

        // ♿ Filtrar por deficiência atendida (E logic)
        if ($request->filled('mat_deficiency_ids')) {
            foreach ($request->mat_deficiency_ids as $deficiencyId) {
                $query->whereHas('deficiencies', function ($q) use ($deficiencyId) {
                    $q->where('deficiencies.id', $deficiencyId);
                });
            }
        }

        // 🌟 Filtro por recursos de acessibilidade específicos (E logic)
        if ($request->filled('mat_accessibility_feature_ids')) {
            foreach ($request->mat_accessibility_feature_ids as $featureId) {
                $query->whereHas('accessibilityFeatures', function ($q) use ($featureId) {
                    $q->where('accessibility_features.id', $featureId);
                });
            }
        }

        // --- LÓGICA DE FILTROS EXCLUDENTES CORRIGIDA ---

        // 📦 Disponibilidade (Disponível vs Indisponível)
        $available = $request->boolean('mat_available');
        $unavailable = $request->boolean('mat_unavailable');

        if ($available && !$unavailable) {
            $query->where('quantity_available', '>', 0);
        } elseif (!$available && $unavailable) {
            $query->where('quantity_available', '=', 0);
        }

        // 🔄 Empréstimos (Com vs Sem Empréstimos Ativos)
        $activeLoans = $request->boolean('mat_active_loans');
        $noLoans = $request->boolean('mat_no_loans');

        if ($activeLoans && !$noLoans) {
            $query->whereHas('loans', function ($q) {
                $q->whereNull('return_date');
            });
        } elseif (!$activeLoans && $noLoans) {
            $query->whereDoesntHave('loans', function ($q) {
                $q->whereNull('return_date');
            });
        }

        return $query;
    }

    public function getLabels(Request $request): array
    {
        $labels = [];

        // 🔎 Busca por nome
        if ($request->filled('mat_name')) {
            $labels[] = "Nome: \"{$request->mat_name}\"";
        }

        // 🧩 Tipo / Categoria
        if ($request->filled('mat_type_id')) {
            $typeName = ResourceType::find($request->mat_type_id)?->name;
            if ($typeName) {
                $labels[] = "Tipo: {$typeName}";
            }
        }

        // 🧱 Estado de conservação
        if ($request->filled('mat_conservation_state')) {
            $state = ConservationState::tryFrom($request->mat_conservation_state);
            $stateLabel = $state?->label() ?? $request->mat_conservation_state;
            $labels[] = "Estado de Conservação: " . $stateLabel;
        }

        // 📊 Status ativo/inativo
        if ($request->has('mat_is_active')) {
            $labels[] = $request->boolean('mat_is_active') ? "Status: Ativo" : "Status: Inativo";
        }

        // 🎓 Requer treinamento
        if ($request->boolean('mat_requires_training')) {
            $labels[] = "Requer Treinamento";
        }

        // ♿ Deficiências (Corrigido para usar o Model Deficiency)
        if ($request->filled('mat_deficiency_ids') && is_array($request->mat_deficiency_ids)) {
            $defNames = \App\Models\SpecializedEducationalSupport\Deficiency::whereIn('id', $request->mat_deficiency_ids)
                ->pluck('name')
                ->unique()
                ->join(', ');

            if ($defNames) {
                $labels[] = "Deficiências: " . $defNames;
            }
        }

        // 🌟 Recursos de acessibilidade
        if ($request->filled('mat_accessibility_feature_ids') && is_array($request->mat_accessibility_feature_ids)) {
            $featNames = AccessibilityFeature::whereIn('id', $request->mat_accessibility_feature_ids)
                ->pluck('name')
                ->unique()
                ->join(', ');

            if ($featNames) {
                $labels[] = "Recursos: " . $featNames;
            }
        }

        // 📦 Disponibilidade
        if ($request->boolean('mat_available'))   $labels[] = "Disponível";
        if ($request->boolean('mat_unavailable')) $labels[] = "Indisponível";

        // 🔄 Empréstimos
        if ($request->boolean('mat_active_loans')) $labels[] = "Com Empréstimos";
        if ($request->boolean('mat_no_loans'))     $labels[] = "Sem Empréstimos";

        return $labels;
    }
}
