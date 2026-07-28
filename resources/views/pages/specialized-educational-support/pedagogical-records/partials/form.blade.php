<input type="hidden" name="attendance_session_id" value="{{ $session->id }}">

<x-forms.section title="Identificação" />

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

<x-forms.section title="Registro Pedagógico" />

@php($isPresent = old('is_present', $record?->is_present ?? true))

<div class="col-md-6">
    <x-forms.input
        name="duration"
        label="Duração"
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
        <label class="form-label fw-bold text-purple-dark" for="presence_0">Aluno Presente</label>
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
                name="planned_performed_activities"
                label="Atividades Planejadas/Realizadas"
                rows="3"
                placeholder="Descreva as atividades planejadas e realizadas..."
                required
                :value="old('planned_performed_activities', $record?->planned_performed_activities)"
            />
        </div>

        <div class="col-md-12">
            <x-forms.textarea
                name="pedagogical_record"
                label="Registro Pedagógico"
                rows="4"
                placeholder="Descreva o acompanhamento pedagógico realizado..."
                required
                :value="old('pedagogical_record', $record?->pedagogical_record)"
            />
        </div>

        <div class="col-md-6">
            <x-forms.textarea
                name="resources_used"
                label="Recursos Utilizados"
                rows="2"
                placeholder="Materiais, instrumentos ou tecnologias utilizados..."
                :value="old('resources_used', $record?->resources_used)"
            />
        </div>

        <div class="col-md-6">
            <x-forms.textarea
                name="general_observations"
                label="Observações Gerais"
                rows="2"
                placeholder="Observações relevantes sobre o atendimento..."
                :value="old('general_observations', $record?->general_observations)"
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
    @vite(['resources/js/pages/specialized-educational-support/session-record-create.js'])
@endpush
