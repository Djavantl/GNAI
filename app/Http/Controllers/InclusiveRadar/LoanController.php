<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\LoanRequest;
use App\Models\InclusiveRadar\Loan;
use App\Services\InclusiveRadar\LoanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoanController extends Controller
{
    protected LoanService $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    public function index(): View
    {
        $loans = $this->loanService->listAll();
        return view('pages.inclusive-radar.loans.index', compact('loans'));
    }

    public function create(): View
    {
        return view('pages.inclusive-radar.loans.create', $this->loanService->getCreateData());
    }

    public function store(LoanRequest $request): RedirectResponse
    {
        try {
            $this->loanService->store($request->validated());
            return redirect()->route('inclusive-radar.loans.index')
                ->with('success', 'Empréstimo realizado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['loanable_id' => $e->getMessage()]);
        }
    }

    public function edit(Loan $loan): View
    {
        return view('pages.inclusive-radar.loans.edit', $this->loanService->getEditData($loan));
    }

    public function update(LoanRequest $request, Loan $loan): RedirectResponse
    {
        $this->loanService->update($loan, $request->validated());
        return redirect()->route('inclusive-radar.loans.index')
            ->with('success', 'Empréstimo atualizado com sucesso!');
    }

    public function returnItem(Request $request, Loan $loan): RedirectResponse
    {
        $this->loanService->markAsReturned($loan, $request->all());
        return redirect()->route('inclusive-radar.loans.index')
            ->with('success', 'Devolução registrada com sucesso!');
    }

    public function destroy(Loan $loan): RedirectResponse
    {
        $this->loanService->delete($loan);
        return redirect()->route('inclusive-radar.loans.index')
            ->with('success', 'Registro de empréstimo removido com sucesso!');
    }

}
