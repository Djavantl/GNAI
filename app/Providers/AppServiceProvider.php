<?php

namespace App\Providers;

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
    }
}
