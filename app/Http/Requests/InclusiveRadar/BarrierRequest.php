<?php

namespace App\Http\Requests\InclusiveRadar;

use App\Enums\InclusiveRadar\BarrierStatus;
use App\Enums\InclusiveRadar\InspectionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class BarrierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'institution_id' => 'required|exists:institutions,id',
            'barrier_category_id' => 'required|exists:barrier_categories,id',
            'priority' => [
                'required',
                Rule::in(['low', 'medium', 'high', 'critical', 'urgent']),
            ],
            'location_id' => 'nullable|exists:locations,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'location_specific_details' => 'nullable|string|max:255',
            'not_applicable' => 'sometimes|boolean',
            'is_anonymous' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'affected_student_id' => 'nullable|exists:students,id',
            'affected_professional_id' => 'nullable|exists:professionals,id',
            'affected_person_name' => 'nullable|string|max:255',
            'affected_person_role' => 'nullable|string|max:255',
            'identified_at' => 'required|date',
            'deficiencies' => 'required|array|min:1',
            'deficiencies.*' => 'exists:deficiencies,id',

            'status' => [
                $isUpdate ? 'nullable' : 'required',
                new Enum(BarrierStatus::class)
            ],
            'inspection_type' => [
                $isUpdate ? 'nullable' : 'required',
                new Enum(InspectionType::class)
            ],
            'inspection_date' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'inspection_description' => 'nullable|string|max:1000',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ];
    }

    protected function prepareForValidation(): void
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        $this->merge([
            'not_applicable' => $this->boolean('not_applicable'),
            'is_anonymous' => $this->boolean('is_anonymous'),
            'is_active' => $this->boolean('is_active'),
            'priority' => $this->priority ?? 'medium',
            'inspection_date' => $this->inspection_date ?? now()->format('Y-m-d'),
        ]);

        if (!$isUpdate) {
            $this->merge([
                'inspection_type' => $this->inspection_type ?? InspectionType::INITIAL->value,
                'status' => $this->status ?? BarrierStatus::IDENTIFIED->value,
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da barreira é obrigatório.',
            'institution_id.required' => 'A instituição é obrigatória.',
            'barrier_category_id.required' => 'A categoria da barreira é obrigatória.',
            'identified_at.required' => 'A data de identificação é obrigatória.',
            'deficiencies.required' => 'Selecione pelo menos um público afetado.',
            'status.required' => 'O status inicial é obrigatório no cadastro.',
            'inspection_date.before_or_equal' => 'A data da vistoria não pode ser no futuro.',
            'images.*.image' => 'O arquivo deve ser uma imagem.',
            'images.*.max' => 'Cada imagem não pode ser maior que 5MB.',
        ];
    }
}
