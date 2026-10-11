<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\UI\Controllers;

use App\Domains\Auth\Application\Resolvers\AuthenticatedUserResolver;
use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Application\Actions\PeiDisciplines\CreatePeiDisciplineAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\PeiDisciplines\DeletePeiDisciplineAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\PeiDisciplines\UpdatePeiDisciplineAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Peis\CreatePeiAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Peis\DeletePeiAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Peis\FinishPeiAction;
use App\Domains\SpecializedEducationalSupport\Application\Data\PeiDisciplines\CreatePeiDisciplineData;
use App\Domains\SpecializedEducationalSupport\Application\Data\PeiDisciplines\UpdatePeiDisciplineData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Peis\ListPeisData;
use App\Domains\SpecializedEducationalSupport\Application\Queries\PeiDisciplines\PeiDisciplineFormQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\PeiDisciplines\TeacherDisciplinesQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Peis\ListAllPeisQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Peis\ListStudentPeisQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Peis\PeiFilterOptionsQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Peis\PeiFormQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Peis\ShowPeiQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidStudent;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Pei;
use App\Domains\SpecializedEducationalSupport\Domain\Models\PeiDiscipline;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Shared\Infrastructure\Pdf\PdfPageNumberer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class PeiController
{
    /**
     * @throws Throwable
     */
    public function all(
        ListPeisData $filters,
        ListAllPeisQuery $query,
        PeiFilterOptionsQuery $filterOptions,
        Request $request,
    ): InertiaResponse {
        $user = $request->user();
        $peis = $query->execute($filters, $user instanceof User ? $user : null);

        $students = $filterOptions->students();
        $semesters = $filterOptions->semesters();

        return Inertia::render(
            'SpecializedEducationalSupport/Peis/All',
            compact('peis', 'students', 'semesters')
        );
    }

    /**
     * @throws Throwable
     */
    public function index(
        Student $student,
        ListPeisData $filters,
        ListStudentPeisQuery $query,
        PeiFilterOptionsQuery $filterOptions,
        Request $request,
    ): InertiaResponse {
        $user = $request->user();
        $peis = $query->execute($student, $filters, $user instanceof User ? $user : null);
        $semesters = $filterOptions->semesters();
        $disciplines = $filterOptions->disciplines();

        return Inertia::render(
            'SpecializedEducationalSupport/Peis/Index',
            compact('peis', 'student', 'semesters', 'disciplines')
        );
    }

    public function show(Pei $pei, ShowPeiQuery $query): InertiaResponse
    {
        $pei = $query->execute($pei);

        $student = $pei->student;
        $studentContext = $pei->studentContext;

        $peiDisciplines = $pei->peiDisciplines()
            ->with(['discipline', 'teacher.person', 'creator'])
            ->latest()
            ->paginate(5);

        return Inertia::render('SpecializedEducationalSupport/Peis/Show', compact(
            'pei', 'student', 'studentContext', 'peiDisciplines'
        ));
    }

    /**
     * @throws InvalidStudent
     */
    public function create(Student $student, PeiFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'SpecializedEducationalSupport/Peis/Create',
            $form->forCreation($student),
        );
    }

    /**
     * @throws Throwable
     */
    public function store(
        Student $student,
        CreatePeiAction $action,
        AuthenticatedUserResolver $authenticatedUser,
        Request $request,
    ): RedirectResponse {
        $pei = $action->execute($student, $authenticatedUser->fromRequest($request));

        return redirect()
            ->route('specialized-educational-support.pei.show', $pei)
            ->with('success', 'PEI gerado com sucesso.');
    }

    /**
     * @throws Throwable
     */
    public function destroy(
        Pei $pei,
        DeletePeiAction $action,
        AuthenticatedUserResolver $authenticatedUser,
        Request $request,
    ): RedirectResponse {
        $studentId = $action->execute($pei, $authenticatedUser->fromRequest($request));

        return redirect()
            ->route('specialized-educational-support.pei.index', $studentId)
            ->with('success', 'PEI removido com sucesso.');
    }

    /**
     * @throws Throwable
     */
    public function finish(
        Pei $pei,
        FinishPeiAction $action,
        AuthenticatedUserResolver $authenticatedUser,
        Request $request,
    ): RedirectResponse {
        $pei = $action->execute($pei, $authenticatedUser->fromRequest($request));

        return redirect()
            ->route('specialized-educational-support.pei.show', $pei)
            ->with('success', 'PEI finalizado com sucesso.');
    }

    /**
     * @throws Throwable
     */
    public function createVersion(
        Student $student,
        CreatePeiAction $action,
        AuthenticatedUserResolver $authenticatedUser,
        Request $request,
    ): RedirectResponse {
        $pei = $action->execute($student, $authenticatedUser->fromRequest($request));

        return redirect()
            ->route('specialized-educational-support.pei.show', $pei)
            ->with('success', 'Nova versão criada com sucesso.');
    }

    public function generateDisciplinePdf(Pei $pei, PeiDiscipline $peiDiscipline): Response
    {
        if ($peiDiscipline->pei_id !== $pei->id) {
            abort(404);
        }

        $pei->load([
            'student.person',
            'student.deficiencies',
            'studentContext',
            'course',
            'semester',
        ]);

        $peiDiscipline->load(['discipline', 'teacher.person']);

        $pdf = Pdf::loadView('pages.specialized-educational-support.peis.pdf', [
            'pei' => $pei,
            'item' => $peiDiscipline,
        ])->setPaper('a4', 'portrait');

        $disciplineName = str_replace(' ', '_', $peiDiscipline->discipline->name);

        PdfPageNumberer::apply($pdf);

        return $pdf->stream("PEI_{$disciplineName}.pdf");
    }

    public function generateCompletePdf(Pei $pei): Response
    {
        $pei->load([
            'student.person',
            'student.deficiencies',
            'studentContext',
            'course',
            'semester',
        ]);

        $peiDisciplines = $pei->peiDisciplines()
            ->with(['discipline', 'teacher.person', 'creator'])
            ->orderBy('id')
            ->get();

        $pdf = Pdf::loadView('pages.specialized-educational-support.peis.pdf-complete', [
            'pei' => $pei,
            'peiDisciplines' => $peiDisciplines,
        ])->setPaper('a4', 'portrait');

        $studentName = str_replace(' ', '_', $pei->student->person->name);

        PdfPageNumberer::apply($pdf);

        return $pdf->stream("PEI_COMPLETO_{$studentName}_v{$pei->version}.pdf");
    }

    public function showDiscipline(Pei $pei, PeiDiscipline $peiDiscipline): InertiaResponse
    {
        $peiDiscipline->load(['teacher.person', 'discipline', 'creator']);
        $student = $pei->student->load('person');

        return Inertia::render('SpecializedEducationalSupport/Peis/Disciplines/Show', compact('pei', 'peiDiscipline', 'student'));
    }

    /**
     * @throws InvalidStudent
     */
    public function createDiscipline(Pei $pei, PeiDisciplineFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'SpecializedEducationalSupport/Peis/Disciplines/Create',
            $form->forCreation($pei),
        );
    }

    /**
     * @throws Throwable
     */
    public function storeDiscipline(
        CreatePeiDisciplineData $data,
        Pei $pei,
        CreatePeiDisciplineAction $action,
        AuthenticatedUserResolver $authenticatedUser,
        Request $request,
    ): RedirectResponse {
        $action->execute($pei, $authenticatedUser->fromRequest($request), $data);

        return redirect()
            ->route('specialized-educational-support.pei.show', $pei)
            ->with('success', 'Adaptação de disciplina adicionada com sucesso.');
    }

    public function editDiscipline(
        Pei $pei,
        PeiDiscipline $peiDiscipline,
        PeiDisciplineFormQuery $form,
        AuthenticatedUserResolver $authenticatedUser,
        Request $request,
    ): InertiaResponse {
        return Inertia::render(
            'SpecializedEducationalSupport/Peis/Disciplines/Edit',
            $form->forUpdate($pei, $peiDiscipline, $authenticatedUser->fromRequest($request)),
        );
    }

    /**
     * @throws Throwable
     */
    public function updateDiscipline(
        UpdatePeiDisciplineData $data,
        Pei $pei,
        PeiDiscipline $peiDiscipline,
        UpdatePeiDisciplineAction $action,
        AuthenticatedUserResolver $authenticatedUser,
        Request $request,
    ): RedirectResponse {
        $action->execute($peiDiscipline, $authenticatedUser->fromRequest($request), $data);

        return redirect()
            ->route('specialized-educational-support.pei.show', $pei)
            ->with('success', 'Adaptação de disciplina atualizada com sucesso.');
    }

    /**
     * @throws Throwable
     */
    public function destroyDiscipline(
        Pei $pei,
        PeiDiscipline $peiDiscipline,
        DeletePeiDisciplineAction $action,
        AuthenticatedUserResolver $authenticatedUser,
        Request $request,
    ): RedirectResponse {
        $action->execute($peiDiscipline, $authenticatedUser->fromRequest($request));

        return redirect()
            ->route('specialized-educational-support.pei.show', $pei)
            ->with('success', 'Adaptação removida com sucesso.');
    }

    public function teacherDisciplines(Request $request, Pei $pei, TeacherDisciplinesQuery $query): JsonResponse
    {
        $teacherId = $request->integer('teacher_id');

        if (! $teacherId) {
            return response()->json([]);
        }

        return response()->json($query->execute($pei, $teacherId));
    }
}
