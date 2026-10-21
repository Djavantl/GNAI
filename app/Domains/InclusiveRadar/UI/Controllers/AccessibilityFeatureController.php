<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\UI\Controllers;

use App\Domains\InclusiveRadar\Application\Actions\AccessibilityFeatures\CreateAccessibilityFeatureAction;
use App\Domains\InclusiveRadar\Application\Actions\AccessibilityFeatures\DeleteAccessibilityFeatureAction;
use App\Domains\InclusiveRadar\Application\Actions\AccessibilityFeatures\UpdateAccessibilityFeatureAction;
use App\Domains\InclusiveRadar\Application\Data\AccessibilityFeatures\CreateAccessibilityFeatureData;
use App\Domains\InclusiveRadar\Application\Data\AccessibilityFeatures\ListAccessibilityFeaturesData;
use App\Domains\InclusiveRadar\Application\Data\AccessibilityFeatures\UpdateAccessibilityFeatureData;
use App\Domains\InclusiveRadar\Application\Queries\AccessibilityFeatures\ListAccessibilityFeaturesQuery;
use App\Domains\InclusiveRadar\Application\Queries\AccessibilityFeatures\ShowAccessibilityFeatureQuery;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidAccessibilityFeature;
use App\Domains\InclusiveRadar\Domain\Models\AccessibilityFeature;
use App\Shared\Infrastructure\Security\RichTextSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

final class AccessibilityFeatureController
{
    public function index(ListAccessibilityFeaturesData $filters, ListAccessibilityFeaturesQuery $query, Request $request): InertiaResponse
    {
        $features = $query->execute($filters);

        return Inertia::render(
            'InclusiveRadar/AccessibilityFeatures/Index',
            [
                'features' => $features,
                'filters' => [
                    'name' => $filters->name ?? '',
                    'is_active' => $filters->isActive === null ? '' : ($filters->isActive ? '1' : '0'),
                ],
                'can' => [
                    'create' => $request->user()->can('accessibility-feature.create'),
                    'show' => $request->user()->can('accessibility-feature.show'),
                    'destroy' => $request->user()->can('accessibility-feature.destroy'),
                ],
            ],
        );
    }

    public function create(Request $request): InertiaResponse
    {
        return Inertia::render('InclusiveRadar/AccessibilityFeatures/Create', [
            'can' => ['store' => $request->user()->can('accessibility-feature.store')],
        ]);
    }

    /**
     * @throws InvalidAccessibilityFeature
     */
    public function store(CreateAccessibilityFeatureData $data, CreateAccessibilityFeatureAction $action): RedirectResponse
    {
        $action->execute($data);

        return redirect()
            ->route('inclusive-radar.accessibility-features.index')
            ->with('success', 'Recurso de acessibilidade criado com sucesso!');
    }

    public function show(AccessibilityFeature $accessibilityFeature, ShowAccessibilityFeatureQuery $query, Request $request): InertiaResponse
    {
        $accessibilityFeature = $query->execute($accessibilityFeature);

        return Inertia::render(
            'InclusiveRadar/AccessibilityFeatures/Show',
            [
                'feature' => $accessibilityFeature,
                'display' => [
                    'descriptionHtml' => RichTextSanitizer::sanitize($accessibilityFeature->description ?: '---'),
                ],
                'can' => [
                    'edit' => $request->user()->can('accessibility-feature.edit'),
                    'destroy' => $request->user()->can('accessibility-feature.destroy'),
                ],
            ],
        );
    }

    public function edit(AccessibilityFeature $accessibilityFeature, Request $request): InertiaResponse
    {
        return Inertia::render(
            'InclusiveRadar/AccessibilityFeatures/Edit',
            [
                'accessibilityFeature' => $accessibilityFeature,
                'can' => ['update' => $request->user()->can('accessibility-feature.update')],
            ],
        );
    }

    /**
     * @throws InvalidAccessibilityFeature
     */
    public function update(UpdateAccessibilityFeatureData $data, AccessibilityFeature $accessibilityFeature, UpdateAccessibilityFeatureAction $action): RedirectResponse
    {
        $action->execute($accessibilityFeature, $data);

        return redirect()
            ->route('inclusive-radar.accessibility-features.index')
            ->with('success', 'Recurso de acessibilidade atualizado com sucesso!');
    }

    /**
     * @throws \Throwable
     */
    public function destroy(AccessibilityFeature $accessibilityFeature, DeleteAccessibilityFeatureAction $action): RedirectResponse
    {
        $action->execute($accessibilityFeature);

        return redirect()
            ->route('inclusive-radar.accessibility-features.index')
            ->with('success', 'Recurso de acessibilidade removido com sucesso!');
    }
}
