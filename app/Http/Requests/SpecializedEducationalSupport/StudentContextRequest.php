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
}
