<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\InstitutionRequest;
use App\Models\InclusiveRadar\Institution;
use App\Services\InclusiveRadar\InstitutionService;
use App\Services\InclusiveRadar\OpenStreetMapService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstitutionController extends Controller
{
    public function __construct(
        protected InstitutionService $service,
        protected OpenStreetMapService $osmService
    ) {}

    public function index(Request $request): View
    {
        $institutions = Institution::query()
            ->with(['locations', 'barriers'])
            ->filterName($request->name)
            ->filterLocation($request->location)
            ->filterStatus($request->is_active)
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view(
                'pages.inclusive-radar.institutions.partials.table',
                compact('institutions')
            );
        }

        return view(
            'pages.inclusive-radar.institutions.index',
            compact('institutions')
        );
    }

    public function create(): View
    {
        return view('pages.inclusive-radar.institutions.create');
    }

    public function store(InstitutionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['latitude']) || empty($data['longitude'])) {
            $address = "{$data['name']}, {$data['city']}, {$data['state']}";
            $coords = $this->osmService->geocode($address);

            if ($coords) {
                $data['latitude'] = $coords['latitude'];
                $data['longitude'] = $coords['longitude'];
            }
        }

        $institution = $this->service->store($data);

        if (!$institution) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Já existe uma instituição cadastrada.');
        }

        return redirect()
            ->route('inclusive-radar.institutions.index')
            ->with('success', 'Instituição criada com sucesso!');
    }

    public function show(Institution $institution): View
    {
        $institution->load(['locations', 'barriers']);

        return view(
            'pages.inclusive-radar.institutions.show',
            compact('institution')
        );
    }

    public function edit(Institution $institution): View
    {
        $institution->load('locations');

        return view(
            'pages.inclusive-radar.institutions.edit',
            compact('institution')
        );
    }

    public function update(InstitutionRequest $request, Institution $institution): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['latitude']) || empty($data['longitude'])) {
            $address = "{$data['name']}, {$data['city']}, {$data['state']}";
            $coords = $this->osmService->geocode($address);

            if ($coords) {
                $data['latitude'] = $coords['latitude'];
                $data['longitude'] = $coords['longitude'];
            }
        }

        $this->service->update($institution, $data);

        return redirect()
            ->route('inclusive-radar.institutions.index')
            ->with('success', 'Instituição atualizada com sucesso!');
    }

    public function toggleActive(Institution $institution): RedirectResponse
    {
        $institution = $this->service->toggleActive($institution);

        $message = $institution->is_active
            ? 'Instituição ativada com sucesso!'
            : 'Instituição desativada com sucesso!';

        return redirect()
            ->route('inclusive-radar.institutions.index')
            ->with('success', $message);
    }

    public function destroy(Institution $institution): RedirectResponse
    {
        $this->service->delete($institution);

        return redirect()
            ->route('inclusive-radar.institutions.index')
            ->with('success', 'Instituição removida com sucesso!');
    }
}
