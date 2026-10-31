<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Controllers;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Application\Actions\Professionals\CreateProfessionalAction;
use App\Domains\Inclusion\Application\Actions\Professionals\DeleteProfessionalAction;
use App\Domains\Inclusion\Application\Actions\Professionals\UpdateProfessionalAction;
use App\Domains\Inclusion\Application\Data\Professionals\CreateProfessionalData;
use App\Domains\Inclusion\Application\Data\Professionals\ListProfessionalsData;
use App\Domains\Inclusion\Application\Data\Professionals\UpdateProfessionalData;
use App\Domains\Inclusion\Application\Queries\Professionals\ListProfessionalsQuery;
use App\Domains\Inclusion\Application\Queries\Professionals\ProfessionalFormQuery;
use App\Domains\Inclusion\Application\Queries\Professionals\ShowProfessionalQuery;
use App\Domains\Inclusion\Domain\Enums\ProfessionalStatus;
use App\Domains\Inclusion\Domain\Models\Professional;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class ProfessionalController
{
    public function index(ListProfessionalsData $filters, ListProfessionalsQuery $query, ProfessionalFormQuery $form, Request $request): InertiaResponse
    {
        $professionals = $query->execute($filters);
        $professionals->through(static function (Professional $professional): Professional {
            $professional->person?->append('photo_url');

            return $professional;
        });

        return Inertia::render(
            'Inclusion/Professionals/Index',
            [
                'professionals' => $professionals,
                ...$form->forIndex(),
                'professionalStatusColors' => collect(ProfessionalStatus::cases())
                    ->mapWithKeys(static fn (ProfessionalStatus $status): array => [
                        $status->value => $status->color(),
                    ]),
                'filters' => [
                    'name' => $filters->name ?? '',
                    'email' => $filters->email ?? '',
                    'registration' => $filters->registration ?? '',
                    'position' => $filters->position === null ? '' : (string) $filters->position,
                    'status' => $filters->status?->value ?? '',
                ],
                'can' => [
                    'create' => $request->user()?->can('professional.create') ?? false,
                    'view' => $request->user()?->can('professional.view') ?? false,
                    'delete' => $request->user()?->can('professional.delete') ?? false,
                ],
            ],
        );
    }

    public function show(Professional $professional, ShowProfessionalQuery $query, Request $request): InertiaResponse
    {
        $professional = $query->execute($professional);
        $professional->person?->append('photo_url');
        $isSuperAdmin = $request->user()?->isSuperAdmin() ?? false;

        return Inertia::render(
            'Inclusion/Professionals/Show',
            [
                'professional' => $professional,
                'display' => [
                    'statusLabel' => $professional->status?->label(),
                    'statusColor' => $professional->status?->color(),
                    'serviceTime' => $professional->entry_date?->diffForHumans(null, true),
                ],
                'impersonationUserId' => $isSuperAdmin
                    ? $professional->user()
                        ->where('is_super_admin', false)
                        ->whereKeyNot($request->user()->getKey())
                        ->value('id')
                    : null,
                'can' => [
                    'update' => $request->user()?->can('professional.update') ?? false,
                    'delete' => $request->user()?->can('professional.delete') ?? false,
                ],
            ],
        );
    }

    public function create(ProfessionalFormQuery $form, Request $request): InertiaResponse
    {
        return Inertia::render(
            'Inclusion/Professionals/Create',
            $form->forCreation($request->user()?->isSuperAdmin() ?? false),
        );
    }

    /**
     * @throws Throwable
     */
    public function store(CreateProfessionalData $data, CreateProfessionalAction $action, Request $request): RedirectResponse
    {
        $user = $request->user();
        $action->execute($data, $user instanceof User ? $user : null);

        return redirect()
            ->route('specialized-educational-support.professionals.index')
            ->with('success', $data->password === null
                ? 'Profissional cadastrado com sucesso. O link para definição da senha foi enviado por e-mail.'
                : 'Profissional cadastrado com sucesso. O acesso já pode ser feito com a senha definida.');
    }

    public function edit(Professional $professional, ProfessionalFormQuery $form, Request $request): InertiaResponse
    {
        return Inertia::render(
            'Inclusion/Professionals/Edit',
            $form->forUpdate($professional, $request->user()?->isSuperAdmin() ?? false),
        );
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateProfessionalData $data, Professional $professional, UpdateProfessionalAction $action, Request $request): RedirectResponse
    {
        $user = $request->user();
        $action->execute($professional, $data, $user instanceof User ? $user : null);

        return redirect()
            ->route('specialized-educational-support.professionals.index')
            ->with('success', 'Profissional atualizado com sucesso.');
    }

    /**
     * @throws Throwable
     */
    public function destroy(Professional $professional, DeleteProfessionalAction $action, Request $request): RedirectResponse
    {
        $user = $request->user();
        $action->execute($professional, $user instanceof User ? $user : null);

        return redirect()
            ->route('specialized-educational-support.professionals.index')
            ->with('success', 'Profissional removido com sucesso.');
    }
}
