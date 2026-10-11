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
    public function index(ListLoansData $filters, ListLoansQuery $query, Request $request): InertiaResponse
    {
        $loans = $query->execute($filters);

        return Inertia::render(
            'InclusiveRadar/Loans/Index',
            compact('loans'),
        );
    }

    public function create(Request $request, LoanFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'InclusiveRadar/Loans/Create',
            $form->forCreation(
                authUser: $request->user(),
                selectedStudentId: $request->integer('student_id') ?: null,
                selectedProfessionalId: $request->integer('professional_id') ?: null,
                selectedItemId: $request->integer('item_id') ?: null,
                selectedItemType: $request->query('item_type'),
            ),
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

    public function show(Loan $loan, ShowLoanQuery $query, Request $request): InertiaResponse
    {
        $loan = $query->execute($loan);

        return Inertia::render(
            'InclusiveRadar/Loans/Show',
            [
                'loan' => $loan,
                'statusLabel' => $loan->statusLabel(),
                'statusColor' => $loan->statusColor(),
                'isOverdue' => $loan->isOverdue(),
                'authUser' => $request->user(),
            ],
        );
    }

    public function edit(Loan $loan, LoanFormQuery $form, Request $request): InertiaResponse
    {
        return Inertia::render(
            'InclusiveRadar/Loans/Edit',
            $form->forUpdate(
                loan: $loan,
                authUser: $request->user(),
            ),
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
            'pages.inclusive-radar.loans.pdf',
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
