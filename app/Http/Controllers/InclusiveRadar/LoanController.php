<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Enums\InclusiveRadar\LoanStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\LoanRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\InclusiveRadar\{Loan, AccessibleEducationalMaterial, AssistiveTechnology};
use App\Models\SpecializedEducationalSupport\{Student, Professional};
use App\Services\InclusiveRadar\LoanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function __construct(
        protected LoanService $service
    ) {}

    public function index(Request $request): View
    {
        $studentName      = $request->student ?? null;
        $professionalName = $request->professional ?? null;
        $status           = $request->status ? LoanStatus::tryFrom($request->status) : null;
        $itemName = $request->item ?? null;

        $loans = Loan::with(['loanable', 'student.person', 'professional.person', 'user'])
            ->student($studentName)
            ->professional($professionalName)
            ->item($itemName)
            ->byStatus($status)
            ->orderByDesc('loan_date')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('pages.inclusive-radar.loans.partials.table', compact('loans'));
        }

        return view('pages.inclusive-radar.loans.index', compact('loans'));
    }

    public function create(): View
    {
        $students = Student::with('person')->get()->sortBy('person.name');
        $professionals = Professional::with('person')->get()->sortBy('person.name');

        $assistiveTechnologies = AssistiveTechnology::where('is_active', true)
            ->whereHas('resourceStatus', fn($q) => $q->where('blocks_loan', false))
            ->with('type')
            ->get()
            ->filter(fn($item) => $item->type?->is_digital || $item->quantity_available > 0);

        $educationalMaterials = AccessibleEducationalMaterial::where('is_active', true)
            ->whereHas('resourceStatus', fn($q) => $q->where('blocks_loan', false))
            ->with('type')
            ->get()
            ->filter(fn($item) => $item->type?->is_digital || $item->quantity_available > 0);

        $authUser = auth()->user();

        return view('pages.inclusive-radar.loans.create', [
            'students' => $students,
            'professionals' => $professionals,
            'assistive_technologies' => $assistiveTechnologies,
            'educational_materials' => $educationalMaterials,
            'authUser' => $authUser,
        ]);
    }

    public function store(LoanRequest $request): RedirectResponse
    {
        try {
            $this->service->store($request->validated());

            return redirect()
                ->route('inclusive-radar.loans.index')
                ->with('success', 'Empréstimo realizado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['loanable_id' => $e->getMessage()]);
        }
    }

    public function show(Loan $loan): View
    {
        $loan->load([
            'loanable',
            'student.person',
            'professional.person',
        ]);

        $authUser = auth()->user();

        return view(
            'pages.inclusive-radar.loans.show',
            compact('loan', 'authUser')
        );
    }

    public function edit(Loan $loan): View
    {
        $students = Student::with('person')->get()->sortBy('person.name');
        $professionals = Professional::with('person')->get()->sortBy('person.name');

        $loan->load(['loanable', 'student.person', 'professional.person']);

        $authUser = auth()->user();

        return view('pages.inclusive-radar.loans.edit', compact(
            'loan',
            'students',
            'professionals',
            'authUser'
        ));
    }

    public function update(LoanRequest $request, Loan $loan): RedirectResponse
    {
        $this->service->update($loan, $request->validated());

        return redirect()
            ->route('inclusive-radar.loans.index')
            ->with('success', 'Empréstimo atualizado com sucesso!');
    }

    public function returnItem(Request $request, Loan $loan): RedirectResponse
    {
        $this->service->markAsReturned($loan, $request->all());

        return redirect()
            ->route('inclusive-radar.loans.index')
            ->with('success', 'Devolução registrada com sucesso!');
    }

    public function destroy(Loan $loan): RedirectResponse
    {
        $this->service->delete($loan);

        return redirect()
            ->route('inclusive-radar.loans.index')
            ->with('success', 'Registro de empréstimo removido com sucesso!');
    }

    public function generatePdf(Loan $loan)
    {
        $loan->load([
            'loanable.type',
            'student.person',
            'professional.person'
        ]);

        $pdf = Pdf::loadView(
            'pages.inclusive-radar.loans.pdf',
            compact('loan')
        )
            ->setPaper('a4', 'portrait')
            ->setOption(['enable_php' => true]);

        return $pdf->stream("Loan_{$loan->id}.pdf");
    }
}
