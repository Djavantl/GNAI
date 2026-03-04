<?php

namespace App\Http\Requests\InclusiveRadar;

use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Models\InclusiveRadar\AssistiveTechnology;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WaitlistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->isMethod('POST')) {
            return [
                'waitlistable_id' => ['required', 'integer'],

                /* A lista de espera é restrita a tecnologias e materiais, garantindo que
                   apenas recursos físicos/digitais do acervo entrem no fluxo de reserva. */
                'waitlistable_type' => [
                    'required', 'string',
                    Rule::in([AssistiveTechnology::class, AccessibleEducationalMaterial::class]),
                ],
                'student_id' => 'nullable|exists:students,id',
                'professional_id' => 'nullable|exists:professionals,id',
                'user_id' => 'required|exists:users,id',
                'observation' => ['nullable', 'string'],
            ];
        }

        return [
            'status' => [
                'nullable',
                Rule::in(['waiting', 'notified', 'fulfilled', 'cancelled']),
            ],
            'observation' => ['nullable', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        /* Automatizamos o registro do usuário responsável pela inserção na fila
           para fins de auditoria, caso não venha explicitamente no request. */
        if (!$this->has('user_id') && auth()->check()) {
            $this->merge(['user_id' => auth()->id()]);
        }
    }

    public function messages(): array
    {
        return [
            'waitlistable_id.required' => 'O recurso é obrigatório.',
            'waitlistable_type.required' => 'O tipo de recurso é obrigatório.',
            'waitlistable_type.in' => 'Tipo de recurso inválido.',
            'student_id.exists' => 'Aluno inválido.',
            'professional_id.exists' => 'Profissional inválido.',
            'user_id.required' => 'O usuário responsável é obrigatório.',
            'status.in' => 'Status inválido.',
        ];
    }
}
