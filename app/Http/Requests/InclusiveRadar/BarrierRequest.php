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
                Rule::in(['low', 'medium', 'high', 'critical']),
            ],
            'location_id' => 'nullable|exists:locations,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'location_specific_details' => 'nullable|string|max:255',

            'is_anonymous' => 'boolean',
            'not_applicable' => 'boolean',

            'affected_student_id' => 'nullable|exists:students,id',
            'affected_professional_id' => 'nullable|exists:professionals,id',

            // Regras básicas para os campos de texto
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

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $isAnonymous = $this->is_anonymous === true;
            $isNotApplicable = $this->not_applicable === true;
            $hasStudent = filled($this->affected_student_id);
            $hasProfessional = filled($this->affected_professional_id);
            $hasPersonName = filled($this->affected_person_name);
            $hasPersonRole = filled($this->affected_person_role);

            // 1. Prioridade Total: Se for Anônimo, ignora as outras exigências.
            if ($isAnonymous) {
                return;
            }

            // 2. Se for Relato Geral (not_applicable), OBRIGA nome e cargo textuais.
            if ($isNotApplicable) {
                if (!$hasPersonName || !$hasPersonRole) {
                    $validator->errors()->add('affected_person_name', 'Para relatos gerais, informe o nome e o cargo da pessoa impactada.');
                }
                return;
            }

            // 3. Se não é Anônimo nem Geral, OBRIGA um Estudante OU Profissional (ou ambos).
            if (!$hasStudent && !$hasProfessional) {
                $msg = 'É necessário informar o estudante ou profissional impactado, ou selecionar uma opção de relato (Anônimo/Geral).';
                $validator->errors()->add('affected_student_id', $msg);
                $validator->errors()->add('affected_professional_id', $msg);
            }
        });
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
