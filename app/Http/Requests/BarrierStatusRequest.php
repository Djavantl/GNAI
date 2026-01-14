<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BarrierStatusRequest extends FormRequest
{
    public function authorize():bool
    {
        return true;
    }

    public function rules(): array
    {
        $status = $this->route('barrierStatus');
        $statusId = $status?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('barrier_statuses', 'name')
                    ->ignore($statusId),
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do status é obrigatório.',
            'name.unique' => 'Já existe um status com este nome.',
            'is_active.boolean' => 'O campo ativo deve ser verdadeiro ou falso.',
        ];
    }
}
