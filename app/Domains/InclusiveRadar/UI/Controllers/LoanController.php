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
            [
                'loans' => $loans->through(fn (Loan $loan): array => $this->loanPayload($loan)),
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
            'InclusiveRadar/Loans/Create',
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

    public function show(Loan $loan, ShowLoanQuery $query, Request $request): InertiaResponse
    {
        $loan = $query->execute($loan);

        return Inertia::render(
            'InclusiveRadar/Loans/Show',
            [
                'loan' => $this->loanPayload($loan),
                'can' => [
                    'edit' => $request->user()?->can('loan.edit') ?? false,
                    'return' => $request->user()?->can('loan.return') ?? false,
                    'destroy' => $request->user()?->can('loan.destroy') ?? false,
                    'pdf' => $request->user()?->can('loan.pdf') ?? false,
                ],
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
            ) + [
                'display' => $this->loanPayload($loan),
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

    /**
     * @return array<string, mixed>
     */
    private function loanPayload(Loan $loan): array
    {
        $loan->loadMissing([
            'loanable',
            'student:id,person_id,registration',
            'student.person:id,name',
            'professional:id,person_id,registration',
            'professional.person:id,name',
            'user:id,name',
        ]);

        return [
            'id' => $loan->id,
            'loanable_id' => $loan->loanable_id,
            'loanable_type' => $loan->loanable_type,
            'loanable_type_label' => $loan->loanableType()->label(),
            'loanable_icon' => $loan->loanableType()->icon(),
            'loanable' => [
                'name' => $loan->loanable?->name ?? 'Item removido',
                'asset_code' => $loan->loanable?->asset_code,
            ],
            'student' => $loan->student ? [
                'name' => $loan->student->person?->name ?? 'Sem nome',
                'registration' => $loan->student->registration,
            ] : null,
            'professional' => $loan->professional ? [
                'name' => $loan->professional->person?->name ?? 'Sem nome',
                'registration' => $loan->professional->registration,
            ] : null,
            'user' => ['name' => $loan->user?->name ?? '---'],
            'loan_date' => $loan->loan_date?->format('Y-m-d\TH:i'),
            'loan_date_label' => $loan->loan_date?->format('d/m/Y H:i') ?? '---',
            'due_date' => $loan->due_date?->format('Y-m-d'),
            'due_date_label' => $loan->due_date?->format('d/m/Y') ?? '---',
            'return_date_label' => $loan->return_date?->format('d/m/Y H:i'),
            'status' => $loan->status?->value,
            'status_label' => $loan->statusLabel(),
            'status_color' => $loan->statusColor(),
            'observation' => $loan->observation,
            'is_active' => $loan->isActive(),
            'is_returned' => $loan->isReturned(),
            'is_overdue' => $loan->isOverdue(),
        ];
    }
}
