<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\StudentContexts\Concerns;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\AttentionLevel;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\AutonomyLevel;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\CommunicationType;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\GeneralLevel;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\InteractionLevel;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\MemoryLevel;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\ReasoningLevel;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\SocializationLevel;
use Illuminate\Validation\Rule;

trait HasStudentContextValidation
{
    public static function rules(): array
    {
        return [
            'history' => ['required', 'string'],
            'specific_educational_needs' => ['required', 'string'],
            'learning_level' => ['nullable', Rule::enum(GeneralLevel::class)],
            'attention_level' => ['nullable', Rule::enum(AttentionLevel::class)],
            'memory_level' => ['nullable', Rule::enum(MemoryLevel::class)],
            'reasoning_level' => ['nullable', Rule::enum(ReasoningLevel::class)],
            'learning_observations' => ['nullable', 'string', 'max:65535'],
            'communication_type' => ['nullable', Rule::enum(CommunicationType::class)],
            'interaction_level' => ['nullable', Rule::enum(InteractionLevel::class)],
            'socialization_level' => ['nullable', Rule::enum(SocializationLevel::class)],
            'shows_aggressive_behavior' => ['sometimes', 'boolean'],
            'shows_withdrawn_behavior' => ['sometimes', 'boolean'],
            'behavior_notes' => ['nullable', 'string', 'max:65535'],
            'autonomy_level' => ['nullable', Rule::enum(AutonomyLevel::class)],
            'needs_mobility_support' => ['nullable', 'string', 'max:65535'],
            'needs_communication_support' => ['nullable', 'string', 'max:65535'],
            'needs_pedagogical_adaptation' => ['nullable', 'string', 'max:65535'],
            'uses_assistive_technology' => ['nullable', 'string', 'max:65535'],
            'has_medical_report' => ['sometimes', 'boolean'],
            'uses_medication' => ['sometimes', 'boolean'],
            'medical_notes' => ['nullable', 'string', 'max:65535'],
            'knowledge' => ['required', 'string', 'max:65535'],
            'difficulties' => ['required', 'string', 'max:65535'],
        ];
    }

    public static function messages(): array
    {
        return [
            'history.required' => 'O histórico educacional é obrigatório.',
            'history.string' => 'O histórico educacional deve ser um texto válido.',
            'specific_educational_needs.required' => 'As necessidades educacionais específicas são obrigatórias.',
            'specific_educational_needs.string' => 'As necessidades educacionais específicas devem ser informadas em um texto válido.',
            'learning_level.enum' => 'O nível de aprendizagem selecionado é inválido.',
            'attention_level.enum' => 'O nível de atenção selecionado é inválido.',
            'memory_level.enum' => 'O nível de memória selecionado é inválido.',
            'reasoning_level.enum' => 'O nível de raciocínio selecionado é inválido.',
            'learning_observations.string' => 'As observações de aprendizagem devem ser informadas em um texto válido.',
            'learning_observations.max' => 'As observações de aprendizagem não podem ultrapassar 65.535 caracteres.',
            'communication_type.enum' => 'O tipo de comunicação selecionado é inválido.',
            'interaction_level.enum' => 'O nível de interação selecionado é inválido.',
            'socialization_level.enum' => 'O nível de socialização selecionado é inválido.',
            'shows_aggressive_behavior.boolean' => 'O campo de comportamento agressivo é inválido.',
            'shows_withdrawn_behavior.boolean' => 'O campo de comportamento retraído é inválido.',
            'behavior_notes.string' => 'As notas comportamentais devem ser informadas em um texto válido.',
            'behavior_notes.max' => 'As notas comportamentais não podem ultrapassar 65.535 caracteres.',
            'autonomy_level.enum' => 'O nível de autonomia selecionado é inválido.',
            'needs_mobility_support.string' => 'A informação sobre apoio de mobilidade deve ser um texto válido.',
            'needs_mobility_support.max' => 'A informação sobre apoio de mobilidade não pode ultrapassar 65.535 caracteres.',
            'needs_communication_support.string' => 'A informação sobre apoio de comunicação deve ser um texto válido.',
            'needs_communication_support.max' => 'A informação sobre apoio de comunicação não pode ultrapassar 65.535 caracteres.',
            'needs_pedagogical_adaptation.string' => 'A informação sobre adaptação pedagógica deve ser um texto válido.',
            'needs_pedagogical_adaptation.max' => 'A informação sobre adaptação pedagógica não pode ultrapassar 65.535 caracteres.',
            'uses_assistive_technology.string' => 'A informação sobre tecnologia assistiva deve ser um texto válido.',
            'uses_assistive_technology.max' => 'A informação sobre tecnologia assistiva não pode ultrapassar 65.535 caracteres.',
            'has_medical_report.boolean' => 'O campo de laudo médico é inválido.',
            'uses_medication.boolean' => 'O campo de uso de medicação é inválido.',
            'medical_notes.string' => 'As observações médicas devem ser informadas em um texto válido.',
            'medical_notes.max' => 'As observações médicas não podem ultrapassar 65.535 caracteres.',
            'knowledge.required' => 'Os conhecimentos e interesses são obrigatórios.',
            'knowledge.string' => 'Os conhecimentos e interesses devem ser informados em um texto válido.',
            'knowledge.max' => 'Os conhecimentos e interesses não podem ultrapassar 65.535 caracteres.',
            'difficulties.required' => 'As dificuldades observadas são obrigatórias.',
            'difficulties.string' => 'As dificuldades observadas devem ser informadas em um texto válido.',
            'difficulties.max' => 'As dificuldades observadas não podem ultrapassar 65.535 caracteres.',
        ];
    }
}
