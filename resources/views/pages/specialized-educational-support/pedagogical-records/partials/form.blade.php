<input type="hidden" name="attendance_session_id" value="{{ $session->id }}">

<x-forms.section title="Identificação do Estudante" />

<x-show.info-item label="Aluno" column="col-md-6" isBox="true">
    {{ $session->students->first()?->person?->name ?? 'Aluno não informado' }}
</x-show.info-item>

<x-show.info-item label="Profissional" column="col-md-6" isBox="true">
    {{ $session->professional?->person?->name ?? 'Profissional não informado' }}
</x-show.info-item>

<x-show.info-item label="Data" column="col-md-4" isBox="true">
    {{ $session->session_date?->format('d/m/Y') ?? '—' }}
</x-show.info-item>

<x-show.info-item label="Horário" column="col-md-4" isBox="true">
    {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}
    às
    {{ $session->end_time ? \Carbon\Carbon::parse($session->end_time)->format('H:i') : '--:--' }}
</x-show.info-item>

<x-show.info-item label="Tipo" column="col-md-4" isBox="true">
    {{ \App\Domains\SpecializedEducationalSupport\Domain\Enums\AttendanceType::labelFor($session->attendance_type) }}
</x-show.info-item>

@php
    $student = $session->students->first();
    $guardians = $student?->guardians?->sortBy('person.name')->values() ?? collect();
    $withGuardians = filter_var(old('with_guardians', $record?->guardians?->isNotEmpty() ?? false), FILTER_VALIDATE_BOOLEAN);
    $selectedGuardianIds = array_map('intval', (array) old('guardian_ids', $record?->guardians?->pluck('id')->all() ?? []));
@endphp

<div class="col-md-12">
    <input type="hidden" name="with_guardians" value="0">
    <x-forms.checkbox
        name="with_guardians"
        id="with_guardians"
        label="Atendimento realizado com responsável pelo aluno"
        :checked="$withGuardians"
        :disabled="$guardians->isEmpty()"
        :description="$guardians->isEmpty()
            ? 'O aluno não possui responsáveis cadastrados no sistema.'
            : 'Marque para selecionar os responsáveis que participaram do atendimento.'"
        class="guardian-participation-toggle"
    />
</div>

<div class="col-md-12 {{ $withGuardians ? '' : 'd-none' }}" id="guardian_participants_fields">
    <div class="border rounded p-3">
        <label class="form-label fw-bold text-purple-dark d-block mb-3">
            Responsáveis participantes
        </label>
        <div class="row g-2">
            @foreach($guardians as $guardian)
                <div class="col-md-6">
                    <div class="form-check">
                        <input
                            class="form-check-input guardian-participant-input"
                            type="checkbox"
                            name="guardian_ids[]"
                            value="{{ $guardian->id }}"
                            id="guardian_{{ $guardian->id }}"
                            {{ in_array((int) $guardian->id, $selectedGuardianIds, true) ? 'checked' : '' }}
                            {{ $withGuardians ? '' : 'disabled' }}
                        >
                        <label class="form-check-label" for="guardian_{{ $guardian->id }}">
                            <strong>{{ $guardian->person->name }}</strong>
                            <span class="text-muted">— {{ $guardian->relationshipLabel() }}</span>
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
        @error('guardian_ids')
            <div class="text-danger small mt-2">{{ $message }}</div>
        @enderror
    </div>
</div>

<x-forms.section title="Acompanhamento Pedagógico" />

@php
    $isPresent = old('is_present', $record?->is_present ?? true);
@endphp

<div class="col-md-12">
    <x-forms.textarea
        name="follow_up_reason"
        label="Motivo do Acompanhamento"
        rows="3"
        placeholder="Informe o motivo do acompanhamento pedagógico..."
        required
        :value="old('follow_up_reason', $record?->follow_up_reason ?? $session->session_objective)"
    />
</div>

<div class="col-md-12">
    <x-forms.input
        name="duration"
        label="Período/Duração do Acompanhamento"
        placeholder="Ex: 50 minutos ou 01:00"
        required
        :value="old('duration', $record?->duration)"
    />
</div>

<div class="col-md-12">
    <div class="form-check form-switch p-3 bg-light rounded border mb-3">
        <input
            class="form-check-input ms-0 me-2 presence-toggle"
            type="checkbox"
            name="is_present"
            id="presence_0"
            value="1"
            {{ filter_var($isPresent, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}
        >
        <label class="form-label fw-bold text-purple-dark" for="presence_0">Presença do Estudante no Atendimento</label>
    </div>
</div>

<div class="col-md-12" id="absence_fields_0" style="display: none;">
    <x-forms.textarea
        name="absence_reason"
        label="Motivo da Ausência"
        rows="2"
        placeholder="Informe o motivo da ausência do aluno..."
        :value="old('absence_reason', $record?->absence_reason)"
        required
    />
</div>

<div class="col-md-12 evaluation-fields" id="eval_fields_0">
    <div class="row g-3">
        <div class="col-md-12">
            <x-forms.textarea
                name="systematic_pedagogical_follow_up_record"
                label="Registro do Acompanhamento Pedagógico Sistemático"
                rows="4"
                placeholder="Registre o acompanhamento pedagógico sistemático realizado..."
                required
                :value="old('systematic_pedagogical_follow_up_record', $record?->systematic_pedagogical_follow_up_record)"
            />
        </div>

        <div class="col-md-12">
            <x-forms.textarea
                name="strategies_and_resources_adopted"
                label="Estratégias e Recursos Adotados (quando necessário)"
                rows="3"
                placeholder="Descreva as estratégias e os recursos adotados, quando necessário..."
                :value="old('strategies_and_resources_adopted', $record?->strategies_and_resources_adopted)"
            />
        </div>

        <div class="col-md-12">
            <x-forms.textarea
                name="referrals_made"
                label="Encaminhamentos Realizados"
                rows="3"
                placeholder="Informe os encaminhamentos realizados..."
                :value="old('referrals_made', $record?->referrals_made)"
            />
        </div>

        <div class="col-md-12">
            <x-forms.textarea
                name="complementary_observations"
                label="Observações Complementares"
                rows="3"
                placeholder="Registre informações complementares relevantes..."
                :value="old('complementary_observations', $record?->complementary_observations)"
            />
        </div>
    </div>
</div>

<div class="col-12 d-flex flex-wrap justify-content-end gap-2 border-t pt-4 px-4 pb-4">
    <x-buttons.link-button
        href="{{ $record ? route('specialized-educational-support.pedagogical-records.show', $record) : route('specialized-educational-support.sessions.show', $session) }}"
        variant="secondary"
    >
        <i class="fas fa-times"></i> Cancelar
    </x-buttons.link-button>

    <x-buttons.submit-button type="submit" class="btn-action new submit">
        <i class="fas fa-save"></i> {{ $submitLabel }}
    </x-buttons.submit-button>
</div>

@push('scripts')
    @vite(['resources/js/pages/specialized-educational-support/attendance-record-form.js'])
@endpush
