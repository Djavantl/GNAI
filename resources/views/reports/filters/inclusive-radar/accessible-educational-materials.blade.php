{{-- Listra roxa ocupando 100% da largura do card --}}
<x-forms.section title="Filtros de Materiais Pedagógicos Acessíveis" class="p-0" />

{{-- Campos de busca --}}
<div class="col-md-4 px-4">
    <x-forms.input name="mat_name" label="Nome" :value="request('mat_name')" placeholder="Digite o nome..." />
</div>

<div class="col-md-4 px-4">
    <x-forms.select
        name="mat_type_id"
        label="Tipo/Categoria"
        :options="$types?->pluck('name','id')->toArray() ?? []"
        :selected="request('mat_type_id')"
    />
</div>

<div class="col-md-4 px-4">
    <x-forms.select
        name="mat_conservation_state"
        label="Estado de Conservação"
        :options="[
        'novo' => 'Novo',
        'bom' => 'Bom',
        'regular' => 'Regular',
        'ruim' => 'Ruim',
        'manutencao' => 'Necessita Manutenção',
        'naoaplicavel' => 'Não se aplica'
    ]"
        :selected="request('mat_conservation_state')"
    />
</div>

{{-- Requer treinamento --}}
<div class="col-12 px-4 mb-4">
    <div class="form-check">
        <input class="form-check-input" type="checkbox"
               name="mat_requires_training"
               id="mat_requires_training"
               value="1"
            {{ request('mat_requires_training') ? 'checked' : '' }}>
        <label class="form-check-label fw-bold text-purple-dark" for="mat_requires_training">
            Requer Treinamento
        </label>
    </div>
</div>

{{-- Deficiências --}}
<div class="col-12 px-4 mb-4">
    <label class="form-label fw-bold text-purple-dark mb-1">Deficiências Atendidas</label>
    <div class="d-flex flex-wrap gap-4 p-3 border rounded bg-light">
        @foreach($deficiencies as $deficiency)
            <x-forms.checkbox
                name="mat_deficiency_ids[]"
                value="{{ $deficiency->id }}"
                label="{{ $deficiency->name }}"
                :checked="is_array(request('mat_deficiency_ids')) && in_array($deficiency->id, request('mat_deficiency_ids', []))"
            />
        @endforeach
    </div>
</div>

{{-- Recursos de acessibilidade --}}
<div class="col-12 px-4 mb-4">
    <label class="form-label fw-bold text-purple-dark mb-1">Recursos de Acessibilidade</label>
    <div class="d-flex flex-wrap gap-4 p-3 border rounded bg-light">
        @foreach($accessibilityFeatures as $feature)
            <x-forms.checkbox
                name="mat_accessibility_feature_ids[]"
                value="{{ $feature->id }}"
                label="{{ $feature->name }}"
                :checked="is_array(request('mat_accessibility_feature_ids')) && in_array($feature->id, request('mat_accessibility_feature_ids', []))"
            />
        @endforeach
    </div>
</div>

{{-- Status e Disponibilidade --}}
<div class="col-12 px-4 mb-4">
    <label class="form-label fw-bold text-purple-dark mb-1">Status e Disponibilidade</label>
    <div class="d-flex flex-wrap gap-4 p-3 border rounded bg-light">
        <x-forms.checkbox name="mat_available" value="1" label="Disponível" :checked="request('mat_available') == '1'" />
        <x-forms.checkbox name="mat_unavailable" value="1" label="Indisponível" :checked="request('mat_unavailable') == '1'" />
        <x-forms.checkbox name="mat_active_loans" value="1" label="Com empréstimos" :checked="request('mat_active_loans') == '1'" />
        <x-forms.checkbox name="mat_no_loans" value="1" label="Sem empréstimos" :checked="request('mat_no_loans') == '1'" />
    </div>
</div>

{{-- Tabela de Resultados --}}
@if(!empty($data['materials']) && count($data['materials']) > 0)
    <x-forms.section title="Resultados: Materiais Pedagógicos" class="p-0" />

    <div class="col-12 px-4 pb-4">
        <x-table.table :headers="['Nome','Tipo','Qtd. Disponível','Status','Requer Treinamento','Deficiências']">
            @foreach($data['materials'] as $item)
                <tr>
                    <x-table.td class="fw-bold text-purple-dark">{{ $item->name }}</x-table.td>
                    <x-table.td>{{ $item->type?->name ?? '-' }}</x-table.td>
                    <x-table.td>{{ $item->quantity_available }}</x-table.td>
                    <x-table.td>
                        <span class="badge bg-{{ $item->is_active ? 'success' : 'danger' }}-subtle text-{{ $item->is_active ? 'success' : 'danger' }} border border-{{ $item->is_active ? 'success' : 'danger' }} px-3">
                            {{ $item->is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </x-table.td>
                    <x-table.td class="text-center">{{ $item->requires_training ? 'Sim' : 'Não' }}</x-table.td>
                    <x-table.td>{{ $item->deficiencies->pluck('name')->join(', ') ?: '-' }}</x-table.td>
                </tr>
            @endforeach
        </x-table.table>
    </div>
@endif
