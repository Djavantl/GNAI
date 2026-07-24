@php
    use Illuminate\Database\Eloquent\Relations\Relation;
@endphp
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Histórico Detalhado - {{ $student->person->name }}</title>
    <x-pdf.styles />
</head>
<body>

<x-pdf.header
    title="Relatório Detalhado de Auditoria"
    :meta="[
        'Aluno' => $student->person->name,
        'Matrícula' => $student->registration,
        'Extraído em' => now()->format('d/m/Y H:i:s'),
    ]"
/>

<table class="audit-table">
    <thead>
        <tr>
            <th class="pdf-w-18">Data / Responsável</th>
            <th class="pdf-w-17">Operação</th>
            <th class="pdf-w-65">Detalhamento das Alterações (De → Para)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($logs as $log)
           @php
            // Mapeamento explícito de auditable_type (caso você use morphMap customizado)
            $auditableMap = [
                'student' => \App\Models\SpecializedEducationalSupport\Student::class,
                'person' => \App\Models\SpecializedEducationalSupport\Person::class,
                'student_deficiency' => \App\Models\SpecializedEducationalSupport\StudentDeficiencies::class,
                'student_document' => \App\Models\SpecializedEducationalSupport\StudentDocument::class,
                'student_course' => \App\Models\SpecializedEducationalSupport\StudentCourse::class,
                'student_context' => \App\Models\SpecializedEducationalSupport\StudentContext::class,
            ];

            $logTypeKey = $log->auditable_type;
            $logModelClass = $auditableMap[$logTypeKey] ?? Relation::getMorphedModel($logTypeKey) ?? $logTypeKey;

            // Campos que normalmente ignoramos (comum)
            $ignoredCommon = ['updated_at', 'created_at', 'deleted_at', 'uploaded_by', 'file_path'];
            // Em casos de submodels queremos preservar student_id e person_id para contexto
            if (in_array($logTypeKey, ['student_deficiency','student_document','student_course','student_context'])) {
                $ignored = $ignoredCommon; // NÃO incluir student_id/person_id
            } else {
                // para student / person, continua ignorando id técnico
                $ignored = array_merge($ignoredCommon, ['person_id','student_id','id']);
            }

            $formatValue = function ($field, $value) use ($logModelClass) {
                if (is_null($value) || $value === '') return '—';
                if (class_exists($logModelClass) && method_exists($logModelClass, 'formatAuditValue')) {
                    $formatted = $logModelClass::formatAuditValue($field, $value);
                    if ($formatted !== null) return $formatted;
                }

                // fallbacks úteis
                if ($field === 'student_id' && $value) {
                    $s = \App\Models\SpecializedEducationalSupport\Student::find($value);
                    return $s ? $s->person->name . " (ID: $value)" : "ID: $value";
                }

                if ($field === 'person_id' && $value) {
                    $p = \App\Models\SpecializedEducationalSupport\Person::find($value);
                    return $p ? $p->name . " (ID: $value)" : "ID: $value";
                }

                if (is_bool($value)) return $value ? 'Sim' : 'Não';
                return \App\Support\RichTextSanitizer::sanitize((string) $value);
            };
        @endphp
            <tr>
                <td>
                    {{ $log->created_at->format('d/m/Y H:i') }}<br>
                    <strong>{{ $log->user?->name ?? 'Sistema' }}</strong>
                </td>
                <td>
                    <span class="module-badge">{{ $modules[$log->auditable_type] ?? strtoupper($log->auditable_type) }}</span><br>
                    <span class="action-label action-{{ $log->action }}">
                        @switch($log->action)
                            @case('created') <i class="fas fa-plus"></i> ADIÇÃO @break
                            @case('updated') <i class="fas fa-edit"></i> EDIÇÃO @break
                            @case('deleted') <i class="fas fa-trash"></i> EXCLUSÃO @break
                            @default {{ strtoupper($log->action) }}
                        @endswitch
                    </span>
                </td>
                <td>
                    <ul class="detail-list">
                        @if($log->action === 'updated')
                            {{-- Lógica de EDIÇÃO: Mostra apenas o que mudou --}}
                            @foreach($allFields as $field)
                                @continue(in_array($field, $ignored))
                                @php
                                    $valOld = $oldValues[$field] ?? null;
                                    $valNew = $newValues[$field] ?? null;
                                @endphp
                                
                                @if($valOld != $valNew)
                                    <li class="detail-item">
                                        <span class="field-name">{{ $fieldLabels[$field] ?? $field }}:</span><br>
                                        <span class="old-val">{!! $formatValue($field, $valOld) !!}</span>
                                        <span class="arrow"> → </span>
                                        <span class="new-val">{!! $formatValue($field, $valNew) !!}</span>
                                    </li>
                                @endif
                            @endforeach
                        @else
                            {{-- Lógica de CRIAÇÃO ou EXCLUSÃO: Mostra todos os dados preenchidos --}}
                            @php $data = ($log->action === 'created') ? $newValues : $oldValues; @endphp
                            @foreach($allFields as $field)
                                @continue(in_array($field, $ignored))
                                @if(!empty($data[$field]))
                                    <li class="detail-item">
                                        <span class="field-name">{{ $fieldLabels[$field] ?? $field }}:</span>
                                        <span>{!! $formatValue($field, $data[$field]) !!}</span>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    </ul>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
