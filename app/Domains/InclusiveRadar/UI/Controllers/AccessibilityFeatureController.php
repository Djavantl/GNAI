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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class AccessibilityFeatureController
{
    public function index(ListAccessibilityFeaturesData $filters, ListAccessibilityFeaturesQuery $query, Request $request): View
    {
        $features = $query->execute($filters);

        if ($request->ajax()) {
            return view(
                'pages.inclusive-radar.accessibility-features.partials.table',
                compact('features'),
            );
        }

        return view(
            'pages.inclusive-radar.accessibility-features.index',
            compact('features'),
        );
    }

    public function create(): View
    {
        return view('pages.inclusive-radar.accessibility-features.create');
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

    public function show(AccessibilityFeature $accessibilityFeature, ShowAccessibilityFeatureQuery $query): View
    {
        $accessibilityFeature = $query->execute($accessibilityFeature);

        return view(
            'pages.inclusive-radar.accessibility-features.show',
            ['feature' => $accessibilityFeature],
        );
    }

    public function edit(AccessibilityFeature $accessibilityFeature): View
    {
        return view(
            'pages.inclusive-radar.accessibility-features.edit',
            compact('accessibilityFeature'),
        );
    }

    /**
     * @throws InvalidAccessibilityFeature
     */
    public function update(UpdateAccessibilityFeatureData $data, AccessibilityFeature $accessibilityFeature, UpdateAccessibilityFeatureAction $action,): RedirectResponse
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
