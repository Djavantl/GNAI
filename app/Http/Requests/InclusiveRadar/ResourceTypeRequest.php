<?php

namespace App\Http\Requests\InclusiveRadar;

use Illuminate\Foundation\Http\FormRequest;

class ResourceTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $typeId = $this->route('resourceType')?->id;

        return [
            'name' => 'required|string|max:255|unique:resource_types,name,' . $typeId,
            'for_assistive_technology' => 'boolean',
            'for_educational_material' => 'boolean',
            'is_digital' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'for_assistive_technology' => $this->boolean('for_assistive_technology'),
            'for_educational_material' => $this->boolean('for_educational_material'),
            'is_digital' => $this->boolean('is_digital'),
            'is_active' => $this->boolean('is_active', true),
        ]);
    }
}
