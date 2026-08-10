<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\Sessions;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\AttendanceType;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\SessionType;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class CreateSessionData extends Data
{
    /**
     * @param  list<int>  $studentIds
     */
    public function __construct(
        public int $professionalId,
        public array $studentIds,
        public string $sessionDate,
        public string $startTime,
        public ?string $endTime,
        public AttendanceType $attendanceType,
        public SessionType $type,
        public string $location,
        public string $sessionObjective,
        public bool $sendNotification = false,
    ) {}

    public static function rules(): array
    {
        return [
            'professional_id' => ['required', 'integer', 'exists:professionals,id'],
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer', 'exists:students,id'],
            'session_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'attendance_type' => ['required', Rule::enum(AttendanceType::class)],
            'type' => ['required', Rule::enum(SessionType::class)],
            'location' => ['required', 'string', 'max:255'],
            'session_objective' => ['required', 'string'],
            'send_notification' => ['sometimes', 'boolean'],
        ];
    }

    public static function messages(): array
    {
        return [
            'student_ids.required' => 'Selecione ao menos um aluno.',
            'student_ids.array' => 'Os alunos do agendamento devem ser informados em uma lista válida.',
            'student_ids.min' => 'Selecione ao menos um aluno.',
            'student_ids.*.exists' => 'Um dos alunos informados não existe.',
            'professional_id.required' => 'O profissional é obrigatório.',
            'professional_id.exists' => 'O profissional informado não existe.',
            'session_date.required' => 'A data do agendamento é obrigatória.',
            'session_date.date' => 'A data do agendamento deve ser válida.',
            'start_time.required' => 'O horário de início é obrigatório.',
            'start_time.date_format' => 'O horário de início deve estar no formato HH:MM.',
            'end_time.date_format' => 'O horário de término deve estar no formato HH:MM.',
            'end_time.after' => 'O horário de término deve ser após o início.',
            'attendance_type.required' => 'O tipo de atendimento é obrigatório.',
            'attendance_type.enum' => 'O tipo de atendimento informado é inválido.',
            'type.required' => 'O tipo de atendimento é obrigatório.',
            'type.enum' => 'O formato do atendimento informado é inválido.',
            'location.required' => 'O local do agendamento é obrigatório.',
            'location.string' => 'O local do agendamento deve ser um texto válido.',
            'location.max' => 'O local do agendamento não pode ultrapassar 255 caracteres.',
            'session_objective.required' => 'O objetivo do agendamento é obrigatório.',
            'session_objective.string' => 'O objetivo do agendamento deve ser um texto válido.',
        ];
    }
}
