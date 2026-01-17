<?php

namespace App\Http\Requests\InclusiveRadar;

use Illuminate\Foundation\Http\FormRequest;

class TypeAttributeAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type_id' => 'required|exists:resource_types,id',
            'attribute_ids' => 'required|array|min:1',
            'attribute_ids.*' => 'exists:type_attributes,id',
        ];
    }

    public function messages(): array
    {
        return [
            'type_id.required' => 'O campo tipo de recurso é obrigatório.',
            'type_id.exists' => 'O tipo de recurso selecionado é inválido.',
            'attribute_ids.required' => 'Selecione pelo menos um atributo para vincular.',
            'attribute_ids.array' => 'Formato de seleção inválido.',
            'attribute_ids.*.exists' => 'Um ou mais atributos selecionados não existem no sistema.',
        ];
    }
}
