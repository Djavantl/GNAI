<?php

namespace App\Http\Controllers\InclusiveRadar;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Enums\InclusiveRadar\BarrierStatus;
use App\Enums\Priority;
use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\BarrierRequest;
use App\Models\InclusiveRadar\{Barrier, BarrierCategory, Inspection, Institution};
use App\Models\SpecializedEducationalSupport\{Deficiency, Professional, Student};
use App\Services\InclusiveRadar\BarrierService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class BarrierController extends Controller
{
    public function __construct(
        private BarrierService $service
    ) {}

    public function index(Request $request): View
    {
        $name = trim($request->name ?? '');

        $barriers = Barrier::with(['category', 'location', 'deficiencies', 'inspections.images', 'registeredBy'])
            ->when($name, fn($q) => $q->name($name))
            ->when($request->category, fn($q) => $q->category($request->category))
            ->when($request->priority, fn($q) => $q->priority($request->priority))
            ->when($request->status, fn($q) => $q->status($request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('pages.inclusive-radar.barriers.partials.table', compact('barriers'));
        }

        return view('pages.inclusive-radar.barriers.index', compact('barriers'));
    }

    public function create(): View
    {
        $data = $this->formData();

        return view('pages.inclusive-radar.barriers.create', $data + [
                'selectedInstitution' => old('institution_id')
                    ? $data['institutions']->firstWhere('id', old('institution_id'))
                    : null,
                'defaultStatus' => BarrierStatus::IDENTIFIED->value,
            ]);
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
            'affectedProfessional.person',
        ]);

        return view('pages.inclusive-radar.barriers.show', compact('barrier'));
    }

    public function edit(Barrier $barrier): View
    {
        $barrier->load(['deficiencies', 'inspections.images', 'location', 'institution', 'category', 'registeredBy']);

        $data = $this->formData();

        return view('pages.inclusive-radar.barriers.edit', $data + [
                'barrier' => $barrier,
                'selectedInstitution' => old('institution_id')
                    ? $data['institutions']->firstWhere('id', old('institution_id'))
                    : ($barrier->institution ?? null),
            ]);
    }

    public function update(BarrierRequest $request, Barrier $barrier): RedirectResponse
    {
        $this->service->update($barrier, $request->validated());

        return redirect()
            ->route('inclusive-radar.barriers.index')
            ->with('success', 'Barreira atualizada com sucesso!');
    }

    public function destroy(Barrier $barrier): RedirectResponse
    {
        $this->service->delete($barrier);

        return redirect()
            ->route('inclusive-radar.barriers.index')
            ->with('success', 'Barreira removida com sucesso!');
    }

    public function showInspection(Barrier $barrier, Inspection $inspection): View
    {
        abort_if(
            $inspection->inspectable_id !== $barrier->id ||
            $inspection->inspectable_type !== $barrier->getMorphClass(),
            403
        );

        $inspection->load(['images', 'inspectable']);

        return view('pages.inclusive-radar.barriers.inspections.show', [
            'barrier' => $barrier,
            'inspection' => $inspection,
        ]);
    }

    public function generatePdf(Barrier $barrier): Response
    {
        $barrier->load([
            'category',
            'location',
            'institution',
            'deficiencies',
            'inspections.images',
            'affectedStudent.person',
            'affectedProfessional.person',
        ]);

        $pdf = Pdf::loadView('pages.inclusive-radar.barriers.pdf', compact('barrier'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'enable_php' => true,
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'chroot' => [public_path(), storage_path()],
            ]);

        return $pdf->stream("Barreira_{$barrier->id}.pdf");
    }

    private function formData(): array
    {
        $institutions = Institution::with(['locations' => fn($q) => $q->where('is_active', true)])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return [
            'institutions' => $institutions,
            'categories' => BarrierCategory::where('is_active', true)->get(),
            'deficiencies' => Deficiency::where('is_active', true)->orderBy('name')->get(),
            'students' => Student::has('person')->with('person')->get()
                ->mapWithKeys(fn($s) => [$s->id => $s->person?->name])
                ->sortBy(fn($name) => $name),
            'professionals' => Professional::has('person')->with('person')->get()
                ->mapWithKeys(fn($p) => [$p->id => $p->person?->name])
                ->sortBy(fn($name) => $name),
            'priorities' => collect(Priority::cases())
                ->mapWithKeys(fn($case) => [$case->value => $case->label()]),
            'barrierStatuses' => collect(BarrierStatus::cases())
                ->mapWithKeys(fn($s) => [$s->value => $s->label()]),
        ];
    }
}
