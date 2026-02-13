<?php

namespace App\Services\Report;

use App\Services\Report\Modules\AccessibleEducationalMaterialReport;
use App\Services\Report\Modules\AssistiveTechnologyReport;
use Illuminate\Http\Request;

class ReportService
{
    public function __construct(
        protected AssistiveTechnologyReport $taModule,
        protected AccessibleEducationalMaterialReport $materialModule
    ) {}

    public function generate(Request $request)
    {
        $result = [
            'data' => [],
            'filters' => [] // agora será array associativo por módulo
        ];

        // Módulo de Tecnologia Assistiva
        if ($request->boolean('ta')) {
            $taData = $this->taModule->generate($request);

            if ($taData->isNotEmpty()) {
                $result['data']['ta'] = $taData;
                $result['filters']['ta'] = $this->taModule->getLabels($request); // filtros isolados de TA
            }
        }

        // Módulo Materiais Pedagógicos
        if ($request->boolean('materials')) {
            $mpaData = $this->materialModule->generate($request);

            if ($mpaData->isNotEmpty()) {
                $result['data']['materials'] = $mpaData;
                $result['filters']['materials'] = $this->materialModule->getLabels($request); // filtros isolados de MPA
            }
        }

        return $result;
    }
}
