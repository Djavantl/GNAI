<?php

namespace App\Http\Requests\InclusiveRadar;

use App\Models\InclusiveRadar\MaintenanceStage;
use Illuminate\Foundation\Http\FormRequest;

class MaintenanceStageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $step = (int) $this->input('step_number');
        $maintenanceId = $this->input('maintenance_id');

        // Regras base que sempre devem existir
        $rules = [
            'maintenance_id' => ['required', 'integer', 'exists:maintenances,id'],
            'step_number'    => ['required', 'integer', 'in:1,2'],
            'observation'    => ['nullable', 'string'],
            'finalize'       => ['nullable', 'in:0,1'],
        ];

        // Verificamos se a etapa atual já está concluída no banco de dados
        $isStepClosed = MaintenanceStage::where('maintenance_id', $maintenanceId)
            ->where('step_number', $step)
            ->whereNotNull('completed_at')
            ->exists();

        // Se a etapa JÁ ESTÁ FECHADA, não validamos os campos (pois virão disabled/vazios)
        if ($isStepClosed) {
            return $rules;
        }

        // Se a etapa ESTÁ ABERTA, aplicamos as validações rigorosas
        if ($step === 1) {
            $rules['estimated_cost']     = ['required', 'numeric', 'min:0'];
            $rules['damage_description'] = ['required', 'string', 'min:5'];
        }

        if ($step === 2) {
            $rules['real_cost']              = ['required', 'numeric', 'min:0'];
            $rules['state']                  = ['required', 'string'];
            $rules['inspection_date']        = ['required', 'date'];
            $rules['inspection_description'] = ['required', 'string', 'min:5'];
            $rules['images']                 = ['nullable', 'array'];
            $rules['images.*']               = ['image', 'mimes:jpeg,png,jpg', 'max:2048'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'maintenance_id.required'         => 'A manutenção é obrigatória.',
            'estimated_cost.required'         => 'O custo estimado é obrigatório.',
            'real_cost.required'              => 'O custo real é obrigatório.',
            'inspection_date.required'        => 'A data da inspeção é obrigatória.',
            'inspection_description.required' => 'O parecer técnico é obrigatório.',
            'damage_description.required'     => 'A descrição do dano é obrigatória.',
            'step_number.in'                  => 'Número da etapa inválido.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('estimated_cost')) {
            $this->merge(['estimated_cost' => $this->sanitizeCurrency($this->estimated_cost)]);
        }

        if ($this->has('real_cost')) {
            $this->merge(['real_cost' => $this->sanitizeCurrency($this->real_cost)]);
        }
    }

    private function sanitizeCurrency($value): ?float
    {
        if (is_null($value) || $value === '') return null;
        if (is_numeric($value)) return (float)$value;

        $clean = str_replace(['R$', ' '], '', $value);
        $clean = str_replace('.', '', $clean);
        $clean = str_replace(',', '.', $clean);

        return (float)$clean;
    }
}
