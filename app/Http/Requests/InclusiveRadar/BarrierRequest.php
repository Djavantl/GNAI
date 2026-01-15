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
            'priority' => ['nullable', Rule::in(['Baixa', 'Média', 'Alta', 'Crítica'])],
            'location_id' => 'required_without:latitude|nullable|exists:locations,id',
            'location_specific_details' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'no_location' => 'sometimes|boolean',

            'barrier_status_id' => 'required|exists:barrier_statuses,id',
            'identified_at' => 'nullable|date',
            'resolved_at' => 'nullable|date|after_or_equal:identified_at',
            'is_active' => 'sometimes|boolean',
            'is_anonymous' => 'sometimes|boolean',

            'deficiencies' => 'required|array|min:1',
            'deficiencies.*' => 'exists:deficiencies,id',

            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_active' => $this->has('is_active'),
            'is_anonymous' => $this->has('is_anonymous'),
            'no_location' => $this->has('no_location'),
        ]);
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da barreira é obrigatório.',
            'barrier_category_id.required' => 'A categoria da barreira é obrigatória.',
            'barrier_category_id.exists' => 'A categoria selecionada é inválida.',
            'location_id.required_without' => 'Selecione um local ou marque o ponto exato no mapa.',
            'location_id.exists' => 'O local selecionado é inválido.',
            'barrier_status_id.required' => 'O status da barreira é obrigatório.',
            'barrier_status_id.exists' => 'O status selecionado é inválido.',
            'priority.in' => 'A prioridade deve ser Baixa, Média, Alta ou Crítica.',
            'resolved_at.after_or_equal' => 'A data de resolução deve ser igual ou posterior à data de identificação.',
            'deficiencies.required' => 'Selecione pelo menos uma deficiência.',
            'deficiencies.*.exists' => 'Uma das deficiências selecionadas é inválida.',
            'images.*.image' => 'O arquivo deve ser uma imagem.',
            'images.*.mimes' => 'A imagem deve ser do tipo: jpeg, png, jpg ou webp.',
            'images.*.max' => 'Cada imagem não pode ser maior que 2MB.',
            'latitude.numeric' => 'Coordenada de latitude inválida.',
            'longitude.numeric' => 'Coordenada de longitude inválida.',
        ];
    }
}
