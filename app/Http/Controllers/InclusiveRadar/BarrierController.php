<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\BarrierRequest;
use App\Models\InclusiveRadar\{Barrier, BarrierCategory, Institution};
use App\Models\SpecializedEducationalSupport\{Deficiency, Professional, Student};
use App\Services\InclusiveRadar\BarrierService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BarrierController extends Controller
{
    public function __construct(
        protected BarrierService $service
    ) {}

    public function index(): View
    {
        $barriers = Barrier::with([
            'category',
            'location',
            'deficiencies',
            'inspections.images',
            'registeredBy'
        ])->latest()->get();

        return view('pages.inclusive-radar.barriers.index', compact('barriers'));
    }

    public function create(): View
    {
        $institutions = Institution::with(['locations' => fn($q) => $q->where('is_active', true)])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $categories = BarrierCategory::where('is_active', true)->get();
        $deficiencies = Deficiency::where('is_active', true)->orderBy('name')->get();
        $students = Student::has('person')->with('person')->get()->sortBy('person.name');
        $professionals = Professional::has('person')->with('person')->get()->sortBy('person.name');

        return view('pages.inclusive-radar.barriers.create', compact(
            'institutions',
            'categories',
            'deficiencies',
            'students',
            'professionals'
        ));
    }

    public function store(BarrierRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()
            ->route('inclusive-radar.barriers.index')
            ->with('success', 'Barreira identificada com sucesso!');
    }

    public function show(Barrier $barrier): View
    {
        $barrier->load([
            'category',
            'location',
            'institution',
            'deficiencies',
            'inspections' => fn($q) => $q->with('images')->latest('inspection_date'),
            'registeredBy',
            'affectedStudent.person',
            'affectedProfessional.person'
        ]);

        return view('pages.inclusive-radar.barriers.show', compact('barrier'));
    }

    public function edit(Barrier $barrier): View
    {

        $barrier->load([
            'deficiencies',
            'inspections.images',
            'location',
            'institution',
            'category',
            'registeredBy'
        ]);

        $institutions = Institution::with(['locations' => fn($q) => $q->where('is_active', true)])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $categories = BarrierCategory::where('is_active', true)->get();
        $deficiencies = Deficiency::where('is_active', true)->orderBy('name')->get();
        $students = Student::has('person')->with('person')->get()->sortBy('person.name');
        $professionals = Professional::has('person')->with('person')->get()->sortBy('person.name');

        return view('pages.inclusive-radar.barriers.edit', compact(
            'barrier',
            'institutions',
            'categories',
            'deficiencies',
            'students',
            'professionals'
        ));
    }

    public function update(BarrierRequest $request, Barrier $barrier): RedirectResponse
    {
        $this->service->update($barrier, $request->validated());

        return redirect()
            ->route('inclusive-radar.barriers.index')
            ->with('success', 'Barreira atualizada com sucesso!');
    }

    public function toggleActive(Barrier $barrier): RedirectResponse
    {
        $this->service->toggleActive($barrier);

        return redirect()->back()->with('success', 'Status da barreira atualizado!');
    }

    public function destroy(Barrier $barrier): RedirectResponse
    {
        $this->service->delete($barrier);

        return redirect()
            ->route('inclusive-radar.barriers.index')
            ->with('success', 'Barreira removida com sucesso!');
    }
}
