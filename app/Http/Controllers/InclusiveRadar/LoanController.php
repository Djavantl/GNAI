<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Enums\InclusiveRadar\LoanStatus;
use App\Enums\InclusiveRadar\ResourceStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\LoanRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\InclusiveRadar\{Loan, AccessibleEducationalMaterial, AssistiveTechnology};
use App\Models\SpecializedEducationalSupport\{Student, Professional};
use App\Services\InclusiveRadar\LoanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class LoanController extends Controller
{
    public function __construct(
        protected LoanService $service
    ) {}

    /*
    |--------------------------------------------------------------------------
    | LISTAGEM
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $studentName      = $request->student ?? null;
        $professionalName = $request->professional ?? null;
        $status           = $request->status
            ? LoanStatus::tryFrom($request->status)
            : null;
        $itemName         = $request->item ?? null;

        $loans = Loan::with([
            'loanable',
            'student.person',
            'professional.person',
            'user'
        ])
            ->student($studentName)
            ->professional($professionalName)
            ->item($itemName)
            ->byStatus($status)
            ->orderByDesc('loan_date')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view(
                'pages.inclusive-radar.loans.partials.table',
                compact('loans')
            );
        }

        return view(
            'pages.inclusive-radar.loans.index',
            compact('loans')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CRIAÇÃO
    |--------------------------------------------------------------------------
    */

    public function create(): View
    {
        $students = Student::with('person')->get()->sortBy('person.name');
        $professionals = Professional::with('person')->get()->sortBy('person.name');

        $assistiveTechnologies = AssistiveTechnology::where('is_active', true)
            ->where('is_loanable', true)
            ->get()
            ->filter(fn($item) => $item->status === ResourceStatus::AVAILABLE)
            ->filter(fn($item) => $item->is_digital || ($item->quantity_available ?? 0) > 0)
            ->map(fn($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'asset_code' => $item->asset_code ?? 'S/N',
                'is_digital' => (bool)$item->is_digital,
                'quantity_available' => $item->quantity_available,
            ])
            ->values();

        $educationalMaterials = AccessibleEducationalMaterial::where('is_active', true)
            ->where('is_loanable', true)
            ->get()
            ->filter(fn($item) => $item->status === ResourceStatus::AVAILABLE)
            ->filter(fn($item) => $item->is_digital || ($item->quantity_available ?? 0) > 0)
            ->map(fn($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'asset_code' => $item->asset_code ?? 'S/N',
                'is_digital' => (bool)$item->is_digital,
                'quantity_available' => $item->quantity_available,
            ])
            ->values();

        return view('pages.inclusive-radar.loans.create', [
            'students'               => $students,
            'professionals'          => $professionals,
            'assistive_technologies' => $assistiveTechnologies,
            'educational_materials'  => $educationalMaterials,
            'authUser'               => auth()->user(),
        ]);
    }

    public function store(LoanRequest $request): RedirectResponse
    {
        try {
            $this->service->store($request->validated());

            return redirect()
                ->route('inclusive-radar.loans.index')
                ->with('success', 'Empréstimo realizado com sucesso!');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'loanable_id' => $e->getMessage()
                ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | VISUALIZAÇÃO
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | EDIÇÃO
    |--------------------------------------------------------------------------
    */

    public function edit(Loan $loan): View
    {
        $students = Student::with('person')
            ->get()
            ->sortBy('person.name');

        $professionals = Professional::with('person')
            ->get()
            ->sortBy('person.name');

        $loan->load([
            'loanable',
            'student.person',
            'professional.person'
        ]);

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
        try {
            $this->service->update(
                $loan,
                $request->validated()
            );

            return redirect()
                ->route('inclusive-radar.loans.index')
                ->with('success', 'Empréstimo atualizado com sucesso!');
        } catch (ValidationException $e) {
            return back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'loan' => $e->getMessage()
                ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DEVOLUÇÃO
    |--------------------------------------------------------------------------
    */

    public function returnItem(Request $request, Loan $loan): RedirectResponse
    {
        try {
            $this->service->markAsReturned($loan, [
                'is_damaged' => $request->boolean('is_damaged'),
                'observation' => $request->input('observation')
            ]);

            return redirect()
                ->route('inclusive-radar.loans.index')
                ->with('success', 'Devolução registrada com sucesso!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Throwable $e) {
            return back()->withErrors([
                'loan' => $e->getMessage()
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | EXCLUSÃO
    |--------------------------------------------------------------------------
    */

    public function destroy(Loan $loan): RedirectResponse
    {
        try {
            $this->service->delete($loan);

            return redirect()
                ->route('inclusive-radar.loans.index')
                ->with('success', 'Registro de empréstimo removido com sucesso!');
        } catch (\Throwable $e) {
            return back()->withErrors([
                'loan' => $e->getMessage()
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PDF
    |--------------------------------------------------------------------------
    */

    public function generatePdf(Loan $loan)
    {
        $loan->load([
            'loanable',
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
