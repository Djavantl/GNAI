<?php

namespace App\Http\Requests\InclusiveRadar;

use Illuminate\Foundation\Http\FormRequest;

class TypeAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $attributeId = $this->route('type_attribute')?->id;

        return [
            'name' => 'required|string|max:255|unique:type_attributes,name,' . $attributeId,
            'label' => 'required|string|max:255',
            'field_type' => 'required|in:string,integer,decimal,boolean,date,text',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_required' => $this->boolean('is_required'),
            'is_active' => $this->boolean('is_active', true),
        ]);
    }
}
