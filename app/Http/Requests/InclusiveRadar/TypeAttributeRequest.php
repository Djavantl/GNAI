<?php

namespace App\Http\Requests\InclusiveRadar;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TypeAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $attribute = $this->route('typeAttribute');
        $attributeId = is_object($attribute) ? $attribute->id : $attribute;

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('type_attributes', 'name')->ignore($attributeId),],
            'label' => ['required', 'string', 'max:255', Rule::unique('type_attributes', 'label')->ignore($attributeId),],
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
