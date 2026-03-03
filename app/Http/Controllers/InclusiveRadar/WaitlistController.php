<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\WaitlistRequest;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Models\InclusiveRadar\Waitlist;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\SpecializedEducationalSupport\Student;
use App\Services\InclusiveRadar\WaitlistService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WaitlistController extends Controller
{
    public function __construct(
        protected WaitlistService $service
    ) {}

    public function index(Request $request): View
    {
        $studentName      = $request->student ?? null;
        $professionalName = $request->professional ?? null;
        $status           = $request->status ?? null;
        $startDate        = $request->start_date ?? null;
        $endDate          = $request->end_date ?? null;

        $waitlists = Waitlist::with([
            'waitlistable',
            'student.person',
            'professional.person',
            'user'
        ])
            ->student($studentName)
            ->professional($professionalName)
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('requested_at', [$startDate, $endDate]))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view(
                'pages.inclusive-radar.waitlists.partials.table',
                compact('waitlists')
            );
        }

        return view(
            'pages.inclusive-radar.waitlists.index',
            compact('waitlists')
        );
    }

    public function create(): View
    {
        $students = Student::with('person')->get()->sortBy('person.name');
        $professionals = Professional::with('person')->get()->sortBy('person.name');

        $assistive_technologies = AssistiveTechnology::get()
            ->filter(fn($item) => $item->quantity_available <= 0 || $item->status->blocksLoan())
            ->sortBy('name');

        $educational_materials = AccessibleEducationalMaterial::get()
            ->filter(fn($item) => $item->quantity_available <= 0 || $item->status->blocksLoan())
            ->sortBy('name');

        $authUser = auth()->user();

        return view(
            'pages.inclusive-radar.waitlists.create',
            compact('students','professionals','assistive_technologies','educational_materials','authUser')
        );
    }

    public function store(WaitlistRequest $request): RedirectResponse
    {
        try {
            $this->service->store($request->validated());

            return redirect()
                ->route('inclusive-radar.waitlists.index')
                ->with('success', 'Solicitação de fila criada com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['waitlistable_id' => $e->getMessage()]);
        }
    }

    public function show(Waitlist $waitlist): View
    {
        $waitlist->load(['waitlistable','student.person','professional.person','user']);
        $authUser = auth()->user();

        return view('pages.inclusive-radar.waitlists.show', compact('waitlist','authUser'));
    }

    public function edit(Waitlist $waitlist): View
    {
        $waitlist->load(['waitlistable','student.person','professional.person','user']);

        $students = Student::with('person')->get()->sortBy('person.name');
        $professionals = Professional::with('person')->get()->sortBy('person.name');

        $assistive_technologies = AssistiveTechnology::get()
            ->sortBy('name');

        $educational_materials = AccessibleEducationalMaterial::get()
            ->sortBy('name');

        $authUser = auth()->user();

        return view(
            'pages.inclusive-radar.waitlists.edit',
            compact('waitlist','students','professionals','assistive_technologies','educational_materials','authUser')
        );
    }

    public function update(WaitlistRequest $request, Waitlist $waitlist): RedirectResponse
    {
        $this->service->update($waitlist, $request->validated());

        return redirect()
            ->route('inclusive-radar.waitlists.index')
            ->with('success', 'Fila atualizada com sucesso!');
    }

    public function destroy(Waitlist $waitlist): RedirectResponse
    {
        $this->service->delete($waitlist);

        return redirect()
            ->route('inclusive-radar.waitlists.index')
            ->with('success', 'Solicitação removida com sucesso!');
    }

    public function cancel(Waitlist $waitlist): RedirectResponse
    {
        $this->service->cancel($waitlist);

        return redirect()
            ->back()
            ->with('success', 'Solicitação cancelada com sucesso!');
    }

    public function generatePdf(Waitlist $waitlist)
    {
        $waitlist->load([
            'waitlistable',
            'student.person',
            'professional.person',
            'user'
        ]);

        $pdf = Pdf::loadView(
            'pages.inclusive-radar.waitlists.pdf',
            compact('waitlist')
        )
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'enable_php' => true,
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'chroot' => [public_path(), storage_path()],
            ]);

        return $pdf->stream("Fila_Espera_{$waitlist->id}.pdf");
    }
}
