<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SpecializedEducationalSupport\PeiRequest;
use App\Http\Requests\SpecializedEducationalSupport\SpecificObjectiveRequest;
use App\Http\Requests\SpecializedEducationalSupport\ContentProgrammaticRequest;
use App\Http\Requests\SpecializedEducationalSupport\MethodologyRequest;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\Pei;
use App\Models\SpecializedEducationalSupport\PeiDiscipline;
use App\Models\SpecializedEducationalSupport\Course;
use App\Models\SpecializedEducationalSupport\Semester;
use App\Models\SpecializedEducationalSupport\Discipline;
use App\Models\SpecializedEducationalSupport\Teacher;
use App\Services\SpecializedEducationalSupport\PeiService;
use App\Services\SpecializedEducationalSupport\PeiDisciplineService;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Requests\SpecializedEducationalSupport\PeiDisciplineRequest;
use Illuminate\Support\Facades\Auth;

class PeiController extends Controller
{
    protected PeiService $peiService;
    protected PeiDisciplineService $peiDisciplineService;

    public function __construct(PeiService $service, PeiDisciplineService $disciplineService)
    {
        $this->service = $service;
        $this->disciplineService = $disciplineService;
    }

    public function all(Request $request)
    {
        $peis = $this->service->all($request->all());

        $students = Student::with('person')
            ->orderBy('id')
            ->get();

        $semesters = Semester::orderByDesc('year')
            ->orderByDesc('term')
            ->get(['id', 'label']);

        if ($request->ajax()) {
            return view(
                'pages.specialized-educational-support.peis.partials.table-all',
                compact('peis')
            )->render();
        }

        return view(
            'pages.specialized-educational-support.peis.all',
            compact('peis', 'students', 'semesters')
        );
    }

    public function index(Student $student, Request $request)
    {
        $peis = $this->service->index($student, $request->all());

        $semesters = Semester::orderByDesc('year')
            ->orderByDesc('term')
            ->get(['id', 'label']);

        $disciplines = Discipline::orderBy('name')
            ->get(['id', 'name']);
 
            

        if ($request->ajax()) {
            return view(
                'pages.specialized-educational-support.peis.partials.table',
                compact('peis')
            )->render();
        }

        return view(
            'pages.specialized-educational-support.peis.index',
            compact('peis', 'student', 'semesters', 'disciplines')
        );
    }

    public function show(Pei $pei)
    {
        $pei->load([
            'student.person',
            'student.deficiencies',
            'studentContext'
        ]);

        $student = $pei->student;
        $studentContext = $pei->studentContext;

        $peiDisciplines = $pei->peiDisciplines()
            ->with(['discipline', 'teacher.person', 'creator'])
            ->latest()
            ->paginate(5);

        return view('pages.specialized-educational-support.peis.show', compact(
            'pei', 'student', 'studentContext', 'peiDisciplines'
        ));
    }

    public function create(Student $student)
    {
        $studentCourse = $student->currentCourse()->first();
        if (!$studentCourse) {
            return redirect()->back()->with('error', 'Este aluno não possui matrícula vigente');
        }
        
        $course = $studentCourse->course;

        $currentContext = $student->contexts()->where('is_current', true)->first();
        if (!$currentContext) {
            return redirect()->back()->with('error', 'Este aluno não possui um contexto atual');
        }

        $semester = Semester::current();
        if (!$semester) {
            return redirect()->back()->with('error', 'O sistema não possui semestre atual configurado');
        }

        return view('pages.specialized-educational-support.peis.create', compact(
            'student', 'studentCourse', 'course', 'currentContext', 'semester'
        ));
    }

    public function store(Student $student)
    {   
        try {
            $pei = $this->service->create($student);
 
            return redirect()
                ->route('specialized-educational-support.pei.show', $pei)
                ->with('success', 'PEI gerado com sucesso.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Pei $pei)
    {
        try {
            $student = $pei->student;
            $this->service->delete($pei);

            return redirect()
                ->route('specialized-educational-support.pei.index', $student)
                ->with('success', 'PEI removido com sucesso.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function finish(Pei $pei)
    {
        try {
            $this->service->finish($pei);

            return redirect()
                ->route('specialized-educational-support.pei.show', $pei)
                ->with('success', 'PEI finalizado com sucesso.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function createVersion(Pei $pei)
    {
        try {
            $new = $this->service->createVersion($pei);

            return redirect()
                ->route('specialized-educational-support.pei.show', $new)
                ->with('success', 'Nova versão criada com sucesso.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function generatePdf(Pei $pei)
    {
        if (! $pei->is_finished) {
            return redirect()
                ->back()
                ->with('error', 'Somente PEIs finalizados podem gerar PDF.');
        }

        $pei->load([
            'student.person',
            'studentContext',
            'peiDisciplines.discipline',
            'peiDisciplines.teacher.person'
        ]);

        $pdf = app('dompdf.wrapper')
            ->loadView('pages.specialized-educational-support.peis.pdf', compact('pei'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("PEI_{$pei->student->person->name}_{$pei->discipline->name}.pdf");
    }

    public function showDiscipline(Pei $pei, PeiDiscipline $peiDiscipline)
    {
        $peiDiscipline->load(['teacher.person', 'discipline', 'creator']);
        $student = $pei->student->load('person');

        return view('pages.specialized-educational-support.peis.disciplines.show', compact('pei', 'peiDiscipline', 'student'));
    }

    /**
     * Exibe formulário de criação de disciplina para o PEI
     */
    public function createDiscipline(Pei $pei)
    {
        if ($pei->is_finished) {
            return redirect()->back()->with('error', 'Não é possível adicionar disciplinas a um PEI finalizado.');
        }

        $teachers = Teacher::all(); // Ou sua lógica de filtro de professores
        $disciplines = Discipline::orderBy('name')->get();

        return view('pages.specialized-educational-support.peis.disciplines.create', compact('pei', 'teachers', 'disciplines'));;
    }

    /**
     * Salva a nova disciplina
     */
    public function storeDiscipline(PeiDisciplineRequest $request, Pei $pei)
    {
        try {
            $this->disciplineService->store($pei, $request->validated());

            return redirect()
                ->route('specialized-educational-support.pei.show', $pei)
                ->with('success', 'Adaptação de disciplina adicionada com sucesso.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Exibe formulário de edição
     */
    public function editDiscipline(Pei $pei, PeiDiscipline $peiDiscipline)
    {
        // O Laravel já garante que $peiDiscipline pertence a $pei por causa do scopeBindings nas rotas
        if ($pei->is_finished) {
            return redirect()->back()->with('error', 'Não é possível editar disciplinas de um PEI finalizado.');
        }

        $teachers = Teacher::all();
        $disciplines = Discipline::orderBy('name')->get();

        return view('pages.specialized-educational-support.peis.disciplines.edit', compact('pei', 'peiDiscipline', 'teachers', 'disciplines'));
    }

    /**
     * Atualiza a disciplina
     */
    public function updateDiscipline(PeiDisciplineRequest $request, Pei $pei, PeiDiscipline $peiDiscipline)
    {
        try {
            $this->disciplineService->update($peiDiscipline, $request->validated());

            return redirect()
                ->route('specialized-educational-support.pei.show', $pei)
                ->with('success', 'Adaptação de disciplina atualizada com sucesso.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove a disciplina
     */
    public function destroyDiscipline(Pei $pei, PeiDiscipline $peiDiscipline)
    {
        try {
            $this->disciplineService->delete($peiDiscipline);

            return redirect()
                ->route('specialized-educational-support.pei.show', $pei)
                ->with('success', 'Adaptação removida com sucesso.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}