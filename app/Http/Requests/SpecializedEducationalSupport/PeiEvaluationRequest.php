<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\SpecializedEducationalSupport\EvaluationType;

class PeiEvaluationRequest extends FormRequest
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
            'evaluation_instruments' => 'required|string', // Instrumentos aplicados 
            'parecer' => 'required|string',                  // Parecer  descritivo 
            'successful_proposals' => 'required|string',    // Propostas com êxito 
            'next_stage_goals' => 'nullable|string',        // Metas para próxima etapa 
        ];
    }
}
