<?php

namespace App\Providers;

use App\Models\InclusiveRadar\ResourceType;
use App\Models\SpecializedEducationalSupport\Deficiency;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Models\InclusiveRadar\Barrier;
use App\Models\InclusiveRadar\Inspection;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Relation::enforceMorphMap([
            'assistive_technology'            => AssistiveTechnology::class,
            'accessible_educational_material' => AccessibleEducationalMaterial::class,
            'barrier'                         => Barrier::class,
            'inspection'                      => Inspection::class,
        ]);

        // View Composer para Accessible Educational Materials
        View::composer(['pages.inclusive-radar.accessible-educational-materials.create',
            'pages.inclusive-radar.accessible-educational-materials.edit',
        ], function ($view) {
            $view->with([
                'deficiencies' => Deficiency::orderBy('name')->get(),
                'resourceTypes' => ResourceType::active()
                    ->forEducationalMaterial()
                    ->orderBy('name')
                    ->get(),
            ]);
        });

        // View Composer para Assistive Technologies (se quiser fazer o mesmo)
        View::composer([
            'pages.inclusive-radar.assistive-technologies.create',
            'pages.inclusive-radar.assistive-technologies.edit',
        ], function ($view) {
            $view->with([
                'deficiencies' => Deficiency::orderBy('name')->get(),
                'resourceTypes' => ResourceType::active()
                    ->forAssistiveTechnology()
                    ->orderBy('name')
                    ->get(),
            ]);
        });
    }
}
