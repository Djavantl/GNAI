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

<div class="col-md-6">
    <x-forms.select
        name="follow_up_status"
        label="Situação do Acompanhamento"
        :options="\App\Domains\SpecializedEducationalSupport\Domain\Enums\PedagogicalFollowUpStatus::options()"
        :selected="old('follow_up_status', $record?->follow_up_status?->value)"
        required
    />
</div>

<div class="col-md-6">
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

        @php
            $failedDisciplineIds = array_map('intval', (array) old('failed_discipline_ids', $record?->failedDisciplines?->pluck('id')->all() ?? []));
            $atRiskDisciplineIds = array_map('intval', (array) old('at_risk_discipline_ids', $record?->atRiskDisciplines?->pluck('id')->all() ?? []));
        @endphp

        <div class="col-md-12">
            <label for="failed-discipline-search" class="form-label fw-bold text-purple-dark">
                Disciplinas com Reprovação no Curso {{ $currentCourse?->name ?? 'Atual do Estudante' }}
            </label>

            @if($disciplines->isEmpty())
                <p class="text-muted border rounded p-3 mb-3">
                    O estudante não possui curso atual com disciplinas cadastradas.
                </p>
            @else
                <input
                    type="search"
                    id="failed-discipline-search"
                    class="form-control mb-3 discipline-search"
                    data-target="failed-discipline-list"
                    placeholder="Buscar disciplina com reprovação..."
                    autocomplete="off"
                >

                <div class="row px-2 mb-3 discipline-list" id="failed-discipline-list">
                    @foreach($disciplines as $discipline)
                        <div class="col-md-4 mb-2 discipline-item" data-name="{{ mb_strtolower($discipline->name) }}">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="failed_discipline_ids[]"
                                    value="{{ $discipline->id }}"
                                    id="failed_discipline_{{ $discipline->id }}"
                                    {{ in_array((int) $discipline->id, $failedDisciplineIds, true) ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="failed_discipline_{{ $discipline->id }}">
                                    {{ $discipline->name }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                    <p class="text-muted small d-none discipline-empty">Nenhuma disciplina encontrada.</p>
                </div>
            @endif
        </div>

        <div class="col-md-12">
            <label for="at-risk-discipline-search" class="form-label fw-bold text-purple-dark">
                Disciplinas com Risco de Insucesso Acadêmico no Curso {{ $currentCourse?->name ?? 'Atual do Estudante' }}
            </label>

            @if($disciplines->isEmpty())
                <p class="text-muted border rounded p-3 mb-3">
                    O estudante não possui curso atual com disciplinas cadastradas.
                </p>
            @else
                <input
                    type="search"
                    id="at-risk-discipline-search"
                    class="form-control mb-3 discipline-search"
                    data-target="at-risk-discipline-list"
                    placeholder="Buscar disciplina com risco acadêmico..."
                    autocomplete="off"
                >

                <div class="row px-2 mb-3 discipline-list" id="at-risk-discipline-list">
                    @foreach($disciplines as $discipline)
                        <div class="col-md-4 mb-2 discipline-item" data-name="{{ mb_strtolower($discipline->name) }}">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="at_risk_discipline_ids[]"
                                    value="{{ $discipline->id }}"
                                    id="at_risk_discipline_{{ $discipline->id }}"
                                    {{ in_array((int) $discipline->id, $atRiskDisciplineIds, true) ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="at_risk_discipline_{{ $discipline->id }}">
                                    {{ $discipline->name }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                    <p class="text-muted small d-none discipline-empty">Nenhuma disciplina encontrada.</p>
                </div>
            @endif
        </div>

        <div class="col-md-12">
            <x-forms.textarea
                name="school_attendance_status"
                label="Situação da Frequência Escolar"
                rows="3"
                placeholder="Descreva a situação da frequência escolar..."
                :value="old('school_attendance_status', $record?->school_attendance_status)"
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.discipline-search').forEach(function (searchInput) {
                searchInput.addEventListener('input', function () {
                    const term = this.value.toLocaleLowerCase('pt-BR').trim();
                    const list = document.getElementById(this.dataset.target);
                    let visibleItems = 0;

                    list.querySelectorAll('.discipline-item').forEach(function (item) {
                        const isVisible = item.dataset.name.includes(term);
                        item.classList.toggle('d-none', !isVisible);
                        visibleItems += isVisible ? 1 : 0;
                    });

                    list.querySelector('.discipline-empty').classList.toggle('d-none', visibleItems !== 0);
                });
            });
        });
    </script>
@endpush
