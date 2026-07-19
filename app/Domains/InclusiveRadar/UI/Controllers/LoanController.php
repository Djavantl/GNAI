<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\UI\Controllers;

use App\Domains\InclusiveRadar\Application\Actions\Loans\CreateLoanAction;
use App\Domains\InclusiveRadar\Application\Actions\Loans\DeleteLoanAction;
use App\Domains\InclusiveRadar\Application\Actions\Loans\ReturnLoanAction;
use App\Domains\InclusiveRadar\Application\Actions\Loans\UpdateLoanAction;
use App\Domains\InclusiveRadar\Application\Data\Loans\CreateLoanData;
use App\Domains\InclusiveRadar\Application\Data\Loans\ListLoansData;
use App\Domains\InclusiveRadar\Application\Data\Loans\ReturnLoanData;
use App\Domains\InclusiveRadar\Application\Data\Loans\UpdateLoanData;
use App\Domains\InclusiveRadar\Application\Queries\Loans\ListLoansQuery;
use App\Domains\InclusiveRadar\Application\Queries\Loans\LoanFormQuery;
use App\Domains\InclusiveRadar\Application\Queries\Loans\LoanPdfQuery;
use App\Domains\InclusiveRadar\Application\Queries\Loans\ShowLoanQuery;
use App\Domains\InclusiveRadar\Domain\Models\Loan;
use App\Domains\InclusiveRadar\UI\Presenters\LoanPresenter;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

final class LoanController extends Controller
{
    public function index(ListLoansData $filters, ListLoansQuery $query, Request $request): View
    {
        $loans = $query->execute($filters);
        $loanPresenter = LoanPresenter::class;

        if ($request->ajax()) {
            return view(
                'pages.inclusive-radar.loans.partials.table',
                compact('loans', 'loanPresenter'),
            );
        }

        return view(
            'pages.inclusive-radar.loans.index',
            compact('loans', 'loanPresenter'),
        );
    }

    public function create(Request $request, LoanFormQuery $form): View
    {
        return view(
            'pages.inclusive-radar.loans.create',
            $form->forCreation(
                selectedStudentId: $request->integer('student_id') ?: null,
                selectedProfessionalId: $request->integer('professional_id') ?: null,
                selectedItemId: $request->integer('item_id') ?: null,
                selectedItemType: $request->query('item_type'),
            ),
        );
    }

    public function store(CreateLoanData $data, CreateLoanAction $action): RedirectResponse
    {
        $action->execute(
            data: $data,
            registeredBy: (int) auth()->id(),
        );

        return redirect()
            ->route('inclusive-radar.loans.index')
            ->with('success', 'Empréstimo realizado com sucesso!');
    }

    public function show(Loan $loan, ShowLoanQuery $query): View
    {
        $loan = $query->execute($loan);

        return view(
            'pages.inclusive-radar.loans.show',
            [
                'loan' => $loan,
                'loanPresenter' => LoanPresenter::class,
                'statusLabel' => LoanPresenter::statusLabel($loan),
                'statusColor' => LoanPresenter::statusColor($loan),
                'isOverdue' => LoanPresenter::isOverdue($loan),
                'authUser' => auth()->user(),
            ],
        );
    }

    public function edit(Loan $loan, LoanFormQuery $form): View
    {
        return view(
            'pages.inclusive-radar.loans.edit',
            $form->forUpdate($loan) + [
                'loanPresenter' => LoanPresenter::class,
            ],
        );
    }

    public function update(UpdateLoanData $data, Loan $loan, UpdateLoanAction $action): RedirectResponse
    {
        $action->execute(
            loan: $loan,
            data: $data,
        );

        return redirect()
            ->route('inclusive-radar.loans.index')
            ->with('success', 'Empréstimo atualizado com sucesso!');
    }

    public function returnItem(ReturnLoanData $data, Loan $loan, ReturnLoanAction $action): RedirectResponse
    {
        $action->execute(
            loan: $loan,
            data: $data,
            returnedBy: auth()->user(),
        );

        return redirect()
            ->route('inclusive-radar.loans.index')
            ->with('success', 'Devolução registrada com sucesso!');
    }

    public function destroy(Loan $loan, DeleteLoanAction $action): RedirectResponse
    {
        $action->execute(
            loan: $loan,
            deletedBy: auth()->user(),
        );

        return redirect()
            ->route('inclusive-radar.loans.index')
            ->with('success', 'Registro de empréstimo removido com sucesso!');
    }

    public function generatePdf(Loan $loan, LoanPdfQuery $query): Response
    {
        $loan = $query->execute($loan);

        $pdf = Pdf::loadView(
            'pages.inclusive-radar.loans.pdf',
            [
                'loan' => $loan,
                'loanPresenter' => LoanPresenter::class,
                'statusLabel' => LoanPresenter::statusLabel($loan),
            ],
        )
            ->setPaper('a4', 'portrait')
            ->setOption(['enable_php' => true]);

        return $pdf->stream("Loan_{$loan->id}.pdf");
    }
}
