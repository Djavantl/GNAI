<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;

class StudentContextRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Histórico educacional
            'history' => ['required', 'string'],

            // Necessidades educacionais específicas
            'specific_educational_needs' => ['required', 'string'],


            // Aprendizagem e cognição
            'learning_level' => ['nullable', 'in:very_low,low,adequate,good,excellent'],
            'attention_level' => ['nullable', 'in:very_low,low,moderate,high'],
            'memory_level' => ['nullable', 'in:low,moderate,good'],
            'reasoning_level' => ['nullable', 'in:concrete,mixed,abstract'],
            'learning_observations' => ['nullable', 'string'],

            // Comunicação e interação
            'communication_type' => ['nullable', 'in:verbal,non_verbal,mixed'],
            'interaction_level' => ['nullable', 'in:very_low,low,moderate,good'],
            'socialization_level' => ['nullable', 'in:isolated,selective,participative'],
            'shows_aggressive_behavior' => ['nullable','boolean'],
            'shows_withdrawn_behavior' => ['nullable','boolean'],
            'behavior_notes' => ['nullable', 'string'],

            // Autonomia e apoio
            'autonomy_level' => ['nullable', 'in:dependent,partial,independent'],
            'needs_mobility_support' => ['nullable', 'string'],
            'needs_communication_support' => ['nullable', 'string'],
            'needs_pedagogical_adaptation' => ['nullable', 'string'],
            'uses_assistive_technology' => ['nullable', 'string'],

            // Saúde
            'has_medical_report' => ['nullable', 'boolean'],
            'uses_medication' => ['nullable', 'boolean'],
            'medical_notes' => ['nullable', 'string'],

            // Avaliação geral
            'knowledge' => ['required', 'string'],
            'difficulties' => ['required', 'string'],
            'finalize_version' => ['sometimes']
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'shows_aggressive_behavior' => $this->boolean('shows_aggressive_behavior'),
            'shows_withdrawn_behavior'  => $this->boolean('shows_withdrawn_behavior'),
            'has_medical_report'        => $this->boolean('has_medical_report'),
            'uses_medication'           => $this->boolean('uses_medication'),
        ]);
    }

    public function messages(): array
    {
        return [
            'history.required' => 'O histórico educacional é obrigatório.',
            'history.string' => 'O histórico educacional deve ser um texto válido.',
            'specific_educational_needs.required' => 'As necessidades educacionais específicas são obrigatórias.',
            'specific_educational_needs.string' => 'As necessidades educacionais específicas devem ser informadas em um texto válido.',
            'learning_level.in' => 'O nível de aprendizagem selecionado é inválido.',
            'attention_level.in' => 'O nível de atenção selecionado é inválido.',
            'memory_level.in' => 'O nível de memória selecionado é inválido.',
            'reasoning_level.in' => 'O nível de raciocínio selecionado é inválido.',
            'learning_observations.string' => 'As observações de aprendizagem devem ser informadas em um texto válido.',
            'communication_type.in' => 'O tipo de comunicação selecionado é inválido.',
            'interaction_level.in' => 'O nível de interação selecionado é inválido.',
            'socialization_level.in' => 'O nível de socialização selecionado é inválido.',
            'shows_aggressive_behavior.boolean' => 'O campo de comportamento agressivo é inválido.',
            'shows_withdrawn_behavior.boolean' => 'O campo de comportamento retraído é inválido.',
            'behavior_notes.string' => 'As notas comportamentais devem ser informadas em um texto válido.',
            'autonomy_level.in' => 'O nível de autonomia selecionado é inválido.',
            'needs_mobility_support.string' => 'A informação sobre apoio de mobilidade deve ser um texto válido.',
            'needs_communication_support.string' => 'A informação sobre apoio de comunicação deve ser um texto válido.',
            'needs_pedagogical_adaptation.string' => 'A informação sobre adaptação pedagógica deve ser um texto válido.',
            'uses_assistive_technology.string' => 'A informação sobre tecnologia assistiva deve ser um texto válido.',
            'has_medical_report.boolean' => 'O campo de laudo médico é inválido.',
            'uses_medication.boolean' => 'O campo de uso de medicação é inválido.',
            'medical_notes.string' => 'As observações médicas devem ser informadas em um texto válido.',
            'knowledge.required' => 'Os conhecimentos e interesses são obrigatórios.',
            'knowledge.string' => 'Os conhecimentos e interesses devem ser informados em um texto válido.',
            'difficulties.required' => 'As dificuldades observadas são obrigatórias.',
            'difficulties.string' => 'As dificuldades observadas devem ser informadas em um texto válido.',
        ];
    }
}
