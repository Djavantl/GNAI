@php
    use Illuminate\Database\Eloquent\Relations\Relation;
@endphp
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Histórico Detalhado - {{ $student->person->name }}</title>
    <style>
        {!! file_get_contents(resource_path('css/components/pdf.css')) !!}
        body { font-family: sans-serif; line-height: 1.4; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 9px; }
        th, td { border: 1px solid #ccc; padding: 8px; vertical-align: top; }
        th { background-color: #f0f0f0; font-weight: bold; text-transform: uppercase; }
        
        .arrow { font-family: DejaVu Sans, sans-serif; color: #777; font-weight: bold; }
        .old-val { color: #b02a37; text-decoration: line-through; background-color: #fff2f2; padding: 1px 2px; }
        .new-val { color: #198754; font-weight: bold; background-color: #f2fff2; padding: 1px 2px; }
        
        .action-label { font-weight: bold; display: block; margin-bottom: 2px; font-size: 10px; }
        .action-created { color: #0d6efd; }
        .action-updated { color: #6610f2; }
        .action-deleted { color: #dc3545; }
        
        .module-badge { 
            display: inline-block; padding: 2px 5px; background: #333; color: #fff; 
            border-radius: 3px; font-size: 8px; margin-bottom: 5px; 
        }
        
        .detail-list { margin: 0; padding-left: 10px; list-style: none; }
        .detail-item { margin-bottom: 6px; border-bottom: 0.5pt solid #eee; padding-bottom: 2px; }
        .field-name { font-weight: bold; color: #444; }
    </style>
</head>
<body>

<div style="text-align:center; border-bottom: 2px solid #000; padding-bottom: 10px;">
    <h2 style="margin: 0;">RELATÓRIO DETALHADO DE AUDITORIA</h2>
    <p style="margin: 5px 0;"><strong>ALUNO:</strong> {{ $student->person->name }} | <strong>MATRÍCULA:</strong> {{ $student->registration }}</p>
    <p style="margin: 0; font-size: 9px;">Extraído em: {{ now()->format('d/m/Y H:i:s') }}</p>
</div>

<table>
    <thead>
        <tr>
            <th style="width: 18%">Data / Responsável</th>
            <th style="width: 17%">Operação</th>
            <th style="width: 65%">Detalhamento das Alterações (De → Para)</th>
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
                return (string)$value;
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