<?php

namespace App\Http\Controllers\InclusiveRadar;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Enums\InclusiveRadar\WaitlistStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\WaitlistRequest;
use App\Models\InclusiveRadar\{AccessibleEducationalMaterial, AssistiveTechnology, Waitlist};
use Illuminate\Http\Response;
use App\Models\SpecializedEducationalSupport\{Professional, Student};
use App\Services\InclusiveRadar\WaitlistService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class WaitlistController extends Controller
{
    public function __construct(
        private WaitlistService $service
    ) {}

    public function index(Request $request): View
    {
        $waitlists = Waitlist::with(['waitlistable', 'student.person', 'professional.person', 'user'])
            ->student($request->student)
            ->professional($request->professional)
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when(
                $request->start_date && $request->end_date,
                fn($q) => $q->whereBetween('requested_at', [$request->start_date, $request->end_date])
            )
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('pages.inclusive-radar.waitlists.partials.table', compact('waitlists'));
        }

        return view('pages.inclusive-radar.waitlists.index', compact('waitlists'));
    }

    public function create(): View
    {
        return view('pages.inclusive-radar.waitlists.create',
            $this->formData() + [
                'assistive_technologies' => $this->waitlistableItems(AssistiveTechnology::class),
                'educational_materials'  => $this->waitlistableItems(AccessibleEducationalMaterial::class),
                'authUser'               => auth()->user(),
            ]
        );
    }

    public function store(WaitlistRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()
            ->route('inclusive-radar.waitlists.index')
            ->with('success', 'Solicitação de fila criada com sucesso!');
    }

    public function show(Waitlist $waitlist): View
    {
        $waitlist->load(['waitlistable', 'student.person', 'professional.person', 'user']);

        $enumStatus = WaitlistStatus::tryFrom($waitlist->status);

        return view('pages.inclusive-radar.waitlists.show', [
            'waitlist'    => $waitlist,
            'authUser'    => auth()->user(),
            'statusLabel' => $enumStatus?->label() ?? $waitlist->status,
            'statusColor' => $enumStatus?->color() ?? 'secondary',
            'canCancel'   => $waitlist->status === WaitlistStatus::WAITING->value,
        ]);
    }

    public function edit(Waitlist $waitlist): View
    {
        $waitlist->load(['waitlistable', 'student.person', 'professional.person', 'user']);

        return view('pages.inclusive-radar.waitlists.edit',
            $this->formData() + [
                'waitlist'    => $waitlist,
                'statusLabel' => WaitlistStatus::tryFrom($waitlist->status)?->label() ?? 'Status Indefinido',
                'authUser'    => auth()->user(),
            ]
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

        return redirect()->back()->with('success', 'Solicitação cancelada com sucesso!');
    }

    public function generatePdf(Waitlist $waitlist): Response
    {
        $waitlist->load(['waitlistable', 'student.person', 'professional.person', 'user']);

        $pdf = Pdf::loadView('pages.inclusive-radar.waitlists.pdf', compact('waitlist'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'enable_php' => true,
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'chroot' => [public_path(), storage_path()],
            ]);

        return $pdf->stream("Fila_Espera_{$waitlist->id}.pdf");
    }

    private function formData(): array
    {
        return [
            'students' => Student::with('person')
                ->get()
                ->sortBy('person.name')
                ->mapWithKeys(fn($s) => [$s->id => $s->person?->name . " ({$s->registration})"]),

            'professionals' => Professional::with('person')
                ->get()
                ->sortBy('person.name')
                ->mapWithKeys(fn($p) => [$p->id => $p->person?->name]),
        ];
    }

    /**
     * Retorna itens indisponíveis para empréstimo (candidatos à fila de espera).
     */
    private function waitlistableItems(string $model): Collection
    {
        return $model::get()
            ->filter(fn($item) => $item->quantity_available <= 0 || $item->status->blocksLoan())
            ->sortBy('name')
            ->values();
    }
}
