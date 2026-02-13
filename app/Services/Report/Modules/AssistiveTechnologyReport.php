<?php

namespace App\Services\Report\Modules;

use App\Enums\InclusiveRadar\ConservationState;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Models\InclusiveRadar\ResourceType;
use App\Models\SpecializedEducationalSupport\Deficiency;
use Illuminate\Http\Request;

class AssistiveTechnologyReport
{
    public function generate(Request $request)
    {
        $query = AssistiveTechnology::query()
            ->with(['type', 'resourceStatus', 'deficiencies']);

        $query = $this->applyFilters($query, $request);

        return $query->get();
    }

    protected function applyFilters($query, Request $request)
    {
        // 🔎 Nome (busca)
        if ($request->filled('ta_name')) {
            $query->where('name', 'like', '%' . $request->ta_name . '%');
        }

        // 🧩 Tipo
        if ($request->filled('ta_type_id')) {
            $query->where('type_id', $request->ta_type_id);
        }

        // 📊 Status
        if ($request->filled('ta_status_id')) {
            $query->where('status_id', $request->ta_status_id);
        }

        // 🔘 Ativo/Inativo
        if ($request->has('ta_is_active')) {
            $query->where('is_active', $request->boolean('ta_is_active'));
        }

        // 🎓 Requer treinamento
        if ($request->boolean('ta_requires_training')) {
            $query->where('requires_training', true);
        }

        // 🧱 Estado de conservação
        if ($request->filled('ta_conservation_state')) {
            $state = ConservationState::tryFrom($request->ta_conservation_state);
            if ($state) {
                $query->where('conservation_state', $state);
            }
        }

        // ♿ Filtrar por deficiências (AND lógico)
        if ($request->filled('ta_deficiency_ids')) {
            foreach ($request->ta_deficiency_ids as $deficiencyId) {
                $query->whereHas('deficiencies', function ($q) use ($deficiencyId) {
                    $q->where('deficiencies.id', $deficiencyId);
                });
            }
        }

        // --- FILTROS BOOLEANOS EM AND ---

        // 📦 Disponibilidade
        if ($request->boolean('ta_available')) {
            $query->where('quantity_available', '>', 0);
        }
        if ($request->boolean('ta_unavailable')) {
            $query->where('quantity_available', '=', 0);
        }

        // 🔄 Empréstimos
        if ($request->boolean('ta_active_loans')) {
            $query->whereHas('loans', function ($q) {
                $q->whereNull('return_date');
            });
        }
        if ($request->boolean('ta_no_loans')) {
            $query->whereDoesntHave('loans', function ($q) {
                $q->whereNull('return_date');
            });
        }

        // 💻 Formato
        if ($request->boolean('ta_digital_only')) {
            $query->whereHas('type', function ($q) {
                $q->where('is_digital', true);
            });
        }
        if ($request->boolean('ta_physical_only')) {
            $query->whereHas('type', function ($q) {
                $q->where('is_digital', false);
            });
        }

        return $query;
    }

    public function getLabels(Request $request): array
    {
        $labels = [];

        // 🔎 Nome
        if ($request->filled('ta_name')) {
            $labels[] = "Nome: \"{$request->ta_name}\"";
        }

        // 🧩 Tipo / Categoria
        if ($request->filled('ta_type_id')) {
            $typeName = ResourceType::find($request->ta_type_id)?->name;
            if ($typeName) {
                $labels[] = "Tipo: {$typeName}";
            }
        }

        // 🎓 Requer treinamento
        if ($request->boolean('ta_requires_training')) {
            $labels[] = "Requer Treinamento";
        }

        // 🧱 Estado de conservação
        if ($request->filled('ta_conservation_state')) {
            $state = ConservationState::tryFrom($request->ta_conservation_state);

            // se o valor do request corresponde a algum enum, usa o label
            // caso contrário, usa o valor bruto do request
            $stateLabel = $state?->label() ?? $request->ta_conservation_state;

            $labels[] = "Estado de Conservação: " . $stateLabel;
        }


        // ♿ Deficiências atendidas (apenas nomes via IDs)
        if ($request->filled('ta_deficiency_ids') && is_array($request->ta_deficiency_ids)) {
            $names = Deficiency::whereIn('id', $request->ta_deficiency_ids)
                ->pluck('name')
                ->unique()
                ->join(', ');

            if ($names) {
                $labels[] = "Deficiências: " . $names;
            }
        }


        // 📦 Disponibilidade
        if ($request->boolean('ta_available'))   $labels[] = "Disponível";
        if ($request->boolean('ta_unavailable')) $labels[] = "Indisponível";

        // 🔄 Empréstimos
        if ($request->boolean('ta_active_loans')) $labels[] = "Com Empréstimos";
        if ($request->boolean('ta_no_loans'))     $labels[] = "Sem Empréstimos";

        // 💻 Formato
        if ($request->boolean('ta_digital_only'))   $labels[] = "Digitais";
        if ($request->boolean('ta_physical_only'))  $labels[] = "Físicos";

        // 🔘 Ativo/Inativo
        if ($request->has('ta_is_active')) {
            $labels[] = $request->boolean('ta_is_active') ? "Status: Ativo" : "Status: Inativo";
        }

        return $labels;
    }
}
