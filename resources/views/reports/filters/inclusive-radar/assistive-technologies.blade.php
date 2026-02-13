{{-- assistive-technologies.blade.php --}}

<x-forms.section title="Filtros de Tecnologias Assistivas" class="px-0" />

{{-- Campos de busca --}}
<div class="col-md-4 px-4">
    <x-forms.input name="ta_name" label="Nome" :value="request('ta_name')" placeholder="Digite o nome..." />
</div>

<div class="col-md-4 px-4">
    <x-forms.select
        name="ta_type_id"
        label="Tipo"
        :options="$types?->pluck('name','id')->toArray() ?? []"
        :selected="request('ta_type_id')"
    />
</div>

<div class="col-md-4 px-4">
    <x-forms.select
        name="ta_conservation_state"
        label="Estado de Conservação"
        :options="[
        'novo' => 'Novo',
        'bom' => 'Bom',
        'regular' => 'Regular',
        'ruim' => 'Ruim',
        'manutencao' => 'Necessita Manutenção',
        'naoaplicavel' => 'Não se aplica'
    ]"
        :selected="request('ta_conservation_state')"
    />
</div>

{{-- Requer treinamento --}}
<div class="col-12 px-4 mb-4">
    <div class="form-check">
        <input class="form-check-input" type="checkbox"
               name="ta_requires_training"
               id="ta_requires_training"
               value="1"
            {{ request('ta_requires_training') ? 'checked' : '' }}>
        <label class="form-check-label fw-bold text-purple-dark" for="ta_requires_training">
            Requer Treinamento
        </label>
    </div>
</div>

{{-- Filtro por Deficiências --}}
<div class="col-12 px-4 mb-4">
    <label class="form-label fw-bold text-purple-dark mb-1">Deficiências Atendidas</label>
    <div class="d-flex flex-wrap gap-4 p-3 border rounded bg-light">
        @foreach($deficiencies as $deficiency)
            <x-forms.checkbox
                name="ta_deficiency_ids[]"
                value="{{ $deficiency->id }}"
                label="{{ $deficiency->name }}"
                :checked="is_array(request('ta_deficiency_ids')) && in_array($deficiency->id, request('ta_deficiency_ids', []))"
            />
        @endforeach
    </div>
</div>

{{-- Status e Disponibilidade --}}
<div class="col-12 px-4 mb-4">
    <label class="form-label fw-bold text-purple-dark mb-1">Status e Disponibilidade</label>
    <div class="d-flex flex-wrap gap-4 p-3 border rounded bg-light">
        <x-forms.checkbox name="ta_available" value="1" label="Disponível" :checked="request('ta_available') == '1'" />
        <x-forms.checkbox name="ta_unavailable" value="1" label="Indisponível" :checked="request('ta_unavailable') == '1'" />
        <x-forms.checkbox name="ta_active_loans" value="1" label="Com Empréstimos" :checked="request('ta_active_loans') == '1'" />
        <x-forms.checkbox name="ta_no_loans" value="1" label="Sem Empréstimos" :checked="request('ta_no_loans') == '1'" />
        <x-forms.checkbox name="ta_digital_only" value="1" label="Digitais" :checked="request('ta_digital_only') == '1'" />
        <x-forms.checkbox name="ta_physical_only" value="1" label="Físicos" :checked="request('ta_physical_only') == '1'" />
    </div>
</div>

{{-- Resultados --}}
@if(!empty($data['ta']) && count($data['ta']) > 0)
    <x-forms.section title="Resultados: Tecnologias Assistivas" class="px-0" />

    <div class="col-12 px-4 pb-4">
        <x-table.table :headers="['Nome','Tipo','Qtd. Disponível','Status','Requer Treinamento','Deficiências']">
            @foreach($data['ta'] as $tech)
                <tr>
                    <x-table.td class="fw-bold text-purple-dark">{{ $tech->name }}</x-table.td>
                    <x-table.td>{{ $tech->type?->name ?? 'N/A' }}</x-table.td>
                    <x-table.td>{{ $tech->quantity_available }}</x-table.td>
                    <x-table.td>
                        <span class="badge bg-{{ $tech->is_active ? 'success' : 'danger' }}-subtle text-{{ $tech->is_active ? 'success' : 'danger' }} border border-{{ $tech->is_active ? 'success' : 'danger' }} px-3">
                            {{ $tech->is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </x-table.td>
                    <x-table.td class="text-center">{{ $tech->requires_training ? 'Sim' : 'Não' }}</x-table.td>
                    <x-table.td>{{ $tech->deficiencies->pluck('name')->join(', ') ?: '---' }}</x-table.td>
                </tr>
            @endforeach
        </x-table.table>
    </div>
@endif
