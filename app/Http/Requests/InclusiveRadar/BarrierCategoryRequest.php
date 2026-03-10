<?php

namespace App\Http\Requests\InclusiveRadar;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BarrierCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $category = $this->route('barrierCategory');

        return [
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('barrier_categories', 'name')
                    ->ignore($category?->id)
                    ->whereNull('deleted_at')
            ],
            'description' => 'nullable|string',
            'blocks_map' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->has('is_active'),
            'blocks_map' => $this->has('blocks_map'),
        ]);
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da categoria é obrigatório.',
            'name.unique' => 'O nome da categoria já está em uso.',
            'blocks_map.boolean' => 'O campo de bloquear mapa deve ser verdadeiro ou falso.',
            'is_active.boolean' => 'O campo ativo deve ser verdadeiro ou falso.',
        ];
    }
}
