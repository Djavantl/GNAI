<?php

namespace App\Http\Requests\InclusiveRadar;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BarrierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'institution_id' => 'required|exists:institutions,id',
            'barrier_category_id' => 'required|exists:barrier_categories,id',
            'barrier_status_id' => 'required|exists:barrier_statuses,id',
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'critical'])],

            'location_id' => 'nullable|exists:locations,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'location_specific_details' => 'nullable|string|max:255',

            'not_applicable' => 'sometimes|boolean',

            'affected_student_id' => 'nullable|exists:students,id',
            'affected_professional_id' => 'nullable|exists:professionals,id',

            'affected_person_name' => 'nullable|string|max:255',
            'affected_person_role' => 'nullable|string|max:255',

            'is_anonymous' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'identified_at' => 'required|date',
            'deficiencies' => 'required|array|min:1',
            'deficiencies.*' => 'exists:deficiencies,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'not_applicable' => $this->boolean('not_applicable'),
            'is_anonymous' => $this->boolean('is_anonymous'),
            'is_active' => $this->boolean('is_active'),
            'priority' => $this->priority ?? 'medium',
        ]);
    }

}
