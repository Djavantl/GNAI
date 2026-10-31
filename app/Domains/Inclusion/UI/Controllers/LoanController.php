<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Controllers;

use App\Domains\Inclusion\Application\Actions\Loans\CreateLoanAction;
use App\Domains\Inclusion\Application\Actions\Loans\DeleteLoanAction;
use App\Domains\Inclusion\Application\Actions\Loans\ReturnLoanAction;
use App\Domains\Inclusion\Application\Actions\Loans\UpdateLoanAction;
use App\Domains\Inclusion\Application\Data\Loans\CreateLoanData;
use App\Domains\Inclusion\Application\Data\Loans\ListLoansData;
use App\Domains\Inclusion\Application\Data\Loans\ReturnLoanData;
use App\Domains\Inclusion\Application\Data\Loans\UpdateLoanData;
use App\Domains\Inclusion\Application\Queries\Loans\ListLoansQuery;
use App\Domains\Inclusion\Application\Queries\Loans\LoanFormQuery;
use App\Domains\Inclusion\Application\Queries\Loans\LoanPdfQuery;
use App\Domains\Inclusion\Application\Queries\Loans\ShowLoanQuery;
use App\Domains\Inclusion\Domain\Models\Loan;
use App\Domains\Inclusion\UI\Presenters\LoanPresenter;
use App\Shared\Infrastructure\Pdf\PdfPageNumberer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class LoanController
{
    public function index(
        ListLoansData $filters,
        ListLoansQuery $query,
        LoanPresenter $presenter,
        Request $request,
    ): InertiaResponse {
        $loans = $query->execute($filters);

        return Inertia::render(
            'Inclusion/Loans/Index',
            [
                'loans' => $loans->through(fn (Loan $loan): array => $presenter->loan($loan)),
                'filters' => $request->only(['item', 'student', 'professional', 'status', 'per_page']),
                'can' => [
                    'create' => $request->user()?->can('loan.create') ?? false,
                    'show' => $request->user()?->can('loan.show') ?? false,
                    'destroy' => $request->user()?->can('loan.destroy') ?? false,
                ],
            ],
        );
    }

    public function create(Request $request, LoanFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'Inclusion/Loans/Create',
            $form->forCreation(
                authUser: $request->user(),
                selectedStudentId: $request->integer('student_id') ?: null,
                selectedProfessionalId: $request->integer('professional_id') ?: null,
                selectedItemId: $request->integer('item_id') ?: null,
                selectedItemType: $request->query('item_type'),
            ) + [
                'can' => ['submit' => $request->user()?->can('loan.store') ?? false],
            ],
        );
    }

    /**
     * @throws Throwable
     */
    public function store(CreateLoanData $data, CreateLoanAction $action, Request $request): RedirectResponse
    {
        $action->execute(
            data: $data,
            registeredBy: (int) $request->user()?->getAuthIdentifier(),
        );

        return redirect()
            ->route('inclusive-radar.loans.index')
            ->with('success', 'Empréstimo realizado com sucesso!');
    }

    public function show(
        Loan $loan,
        ShowLoanQuery $query,
        LoanPresenter $presenter,
        Request $request,
    ): InertiaResponse {
        $loan = $query->execute($loan);

        return Inertia::render(
            'Inclusion/Loans/Show',
            [
                'loan' => $presenter->loan($loan),
                'can' => [
                    'edit' => $request->user()?->can('loan.edit') ?? false,
                    'return' => $request->user()?->can('loan.return') ?? false,
                    'destroy' => $request->user()?->can('loan.destroy') ?? false,
                    'pdf' => $request->user()?->can('loan.pdf') ?? false,
                ],
            ],
        );
    }

    public function edit(
        Loan $loan,
        LoanFormQuery $form,
        LoanPresenter $presenter,
        Request $request,
    ): InertiaResponse {
        return Inertia::render(
            'Inclusion/Loans/Edit',
            $form->forUpdate(
                loan: $loan,
                authUser: $request->user(),
            ) + [
                'display' => $presenter->loan($loan),
                'can' => ['submit' => $request->user()?->can('loan.update') ?? false],
            ],
        );
    }

    /**
     * @throws Throwable
     */
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

    /**
     * @throws Throwable
     */
    public function returnItem(ReturnLoanData $data, Loan $loan, ReturnLoanAction $action, Request $request): RedirectResponse
    {
        $action->execute(
            loan: $loan,
            data: $data,
            returnedBy: $request->user(),
        );

        return redirect()
            ->route('inclusive-radar.loans.index')
            ->with('success', 'Devolução registrada com sucesso!');
    }

    public function destroy(Loan $loan, DeleteLoanAction $action, Request $request): RedirectResponse
    {
        $action->execute(
            loan: $loan,
            deletedBy: $request->user(),
        );

        return redirect()
            ->route('inclusive-radar.loans.index')
            ->with('success', 'Registro de empréstimo removido com sucesso!');
    }

    public function generatePdf(Loan $loan, LoanPdfQuery $query): Response
    {
        $loan = $query->execute($loan);

        $pdf = Pdf::loadView(
            'pages.inclusion.loans.pdf',
            [
                'loan' => $loan,
                'statusLabel' => $loan->statusLabel(),
            ],
        )
            ->setPaper('a4', 'portrait');

        PdfPageNumberer::apply($pdf);

        return $pdf->stream("Loan_{$loan->id}.pdf");
    }
}
