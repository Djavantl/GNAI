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
            'shows_aggressive_behavior' => ['boolean'],
            'shows_withdrawn_behavior' => ['boolean'],
            'behavior_notes' => ['nullable', 'string'],

            // Autonomia e apoio
            'autonomy_level' => ['nullable', 'in:dependent,partial,independent'],
            'needs_mobility_support' => ['boolean'],
            'needs_communication_support' => ['boolean'],
            'needs_pedagogical_adaptation' => ['boolean'],
            'uses_assistive_technology' => ['boolean'],

            // Saúde
            'has_medical_report' => ['boolean'],
            'uses_medication' => ['boolean'],
            'medical_notes' => ['nullable', 'string'],

            // Avaliação geral
            'strengths' => ['nullable', 'string'],
            'difficulties' => ['nullable', 'string'],
            'recommendations' => ['nullable', 'string'],
            'general_observation' => ['nullable', 'string'],
        ];
    }
}
