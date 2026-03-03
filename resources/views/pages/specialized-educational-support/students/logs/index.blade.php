@extends('layouts.master')

@section('title', "Histórico - " . $student->person->name)

@section('content')

<div class="mb-4">
    <x-breadcrumb :items="[
        'Home' => route('dashboard'),
        'Alunos' => route('specialized-educational-support.students.index'),
        $student->person->name => route('specialized-educational-support.students.show', $student),
        'Histórico Consolidado' => null
    ]" />
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="text-title">Histórico de Alterações</h2>
        <p class="text-muted">
            Rastreabilidade completa de <strong>{{ $student->person->name }}</strong>
        </p>
    </div>

    <div class="d-flex gap-2">
        <x-buttons.link-button 
            href="{{ route('specialized-educational-support.students.show', $student) }}" 
            variant="secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </x-buttons.link-button>

        <x-buttons.pdf-button 
            :href="route('specialized-educational-support.students.logs.pdf', $student)" />
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">

        @php
            use Illuminate\Database\Eloquent\Relations\Relation;

            $modules = [
                'student' => 'Dados Acadêmicos',
                'person' => 'Dados Pessoais',
                'student_deficiency' => 'Deficiência',
                'student_document' => 'Documento',
                'student_course' => 'Curso/Matrícula',
                'student_context' => 'Contexto Educacional'
            ];
        @endphp

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 180px">Data</th>
                        <th style="width: 160px">Responsável</th>
                        <th style="width: 160px">Módulo</th>
                        <th style="width: 140px">Ação</th>
                        <th>Detalhamento</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($logs as $log)

                    @php
                        $logModelClass = Relation::getMorphedModel($log->auditable_type) ?? $log->auditable_type;
                        $oldValues = $log->old_values ?? [];
                        $newValues = $log->new_values ?? [];

                        $ignored = ['updated_at','created_at','deleted_at','id'];

                        $allFields = array_unique(array_merge(
                            array_keys($oldValues),
                            array_keys($newValues)
                        ));

                        $formatValue = function ($field, $value) use ($logModelClass) {
                            if ($value === null || $value === '') return '—';

                            if (class_exists($logModelClass) && method_exists($logModelClass, 'formatAuditValue')) {
                                $formatted = $logModelClass::formatAuditValue($field, $value);
                                if ($formatted !== null) return $formatted;
                            }

                            if (is_bool($value)) return $value ? 'Sim' : 'Não';

                            return $value;
                        };

                        $actionLabels = [
                            'created' => ['label' => 'Adicionado', 'class' => 'success', 'icon' => 'plus'],
                            'updated' => ['label' => 'Editado', 'class' => 'primary', 'icon' => 'edit'],
                            'deleted' => ['label' => 'Removido', 'class' => 'danger', 'icon' => 'trash'],
                        ];

                        $action = $actionLabels[$log->action] ?? [
                            'label' => strtoupper($log->action),
                            'class' => 'secondary',
                            'icon' => 'circle'
                        ];
                    @endphp

                    <tr>
                        <td>
                            <div>{{ $log->created_at->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ $log->created_at->format('H:i') }}</small>
                        </td>

                        <td>
                            {{ $log->user?->name ?? 'Sistema' }}
                        </td>

                        <td>
                            <span class="badge bg-dark">
                                {{ $modules[$log->auditable_type] ?? $log->auditable_type }}
                            </span>
                        </td>

                        <td>
                            <span class="badge bg-{{ $action['class'] }}">
                                <i class="fas fa-{{ $action['icon'] }}"></i>
                                {{ $action['label'] }}
                            </span>
                        </td>

                        <td>
                            <ul class="mb-0 ps-3">

                                @if($log->action === 'updated')

                                    @foreach($allFields as $field)
                                        @continue(in_array($field, $ignored))

                                        @php
                                            $old = $oldValues[$field] ?? null;
                                            $new = $newValues[$field] ?? null;
                                        @endphp

                                        @if($old != $new)
                                            <li>
                                                <strong>{{ $fieldLabels[$field] ?? $field }}:</strong>
                                                <span class="text-danger text-decoration-line-through">
                                                    {{ $formatValue($field, $old) }}
                                                </span>
                                                →
                                                <span class="text-success fw-bold">
                                                    {{ $formatValue($field, $new) }}
                                                </span>
                                            </li>
                                        @endif
                                    @endforeach

                                @else

                                    @php
                                        $data = $log->action === 'created' ? $newValues : $oldValues;
                                    @endphp

                                    @foreach($allFields as $field)
                                        @continue(in_array($field, $ignored))
                                        @if(isset($data[$field]))
                                            <li>
                                                <strong>{{ $fieldLabels[$field] ?? $field }}:</strong>
                                                {{ $formatValue($field, $data[$field]) }}
                                            </li>
                                        @endif
                                    @endforeach

                                @endif

                            </ul>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="5" class="text-center p-4 text-muted">
                            Nenhum registro de auditoria encontrado.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

    </div>

    @if($logs->hasPages())
        <div class="card-footer">
            {{ $logs->links() }}
        </div>
    @endif
</div>

@endsection
