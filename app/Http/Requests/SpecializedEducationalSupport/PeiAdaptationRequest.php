<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;

class PeiAdaptationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'course_subject' => 'required|string|max:255', // Componente Curricular 
            'teacher_name' => 'required|string|max:255',   // Docente 
            'specific_objectives' => 'required|string',    // Definidos a partir do componente 
            'content_programmatic' => 'required|string',   // Priorização de conteúdos 
            'methodology_strategies' => 'required|string', // Recursos e estratégias 
        ];
    }
}
