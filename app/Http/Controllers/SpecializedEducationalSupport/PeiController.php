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
use App\Models\SpecializedEducationalSupport\Course;
use App\Models\SpecializedEducationalSupport\Semester;
use App\Models\SpecializedEducationalSupport\Discipline;
use App\Models\SpecializedEducationalSupport\SpecificObjective;
use App\Models\SpecializedEducationalSupport\ContentProgrammatic;
use App\Models\SpecializedEducationalSupport\Methodology;
use App\Services\SpecializedEducationalSupport\PeiService;
use App\Enums\SpecializedEducationalSupport\ObjectiveStatus;
use Barryvdh\DomPDF\Facade\Pdf;

class PeiController extends Controller
{
    protected PeiService $service;

    public function __construct(PeiService $service)
    {
        $this->service = $service;
    }

    public function all()
    {
        $peis = Pei::with(['student.person', 'discipline', 'semester', 'course'])->get();
        $semester = Semester::current();
        
        return view('pages.specialized-educational-support.peis.all', compact('peis', 'semester'));
    }

    public function index(Student $student)
    {
        $peis = $this->service->index($student);
        return view('pages.specialized-educational-support.peis.index', compact('student', 'peis'));
    }

    public function show(Pei $pei)
    {
        $pei = $this->service->show($pei);
        $student = $pei->student;
        $statuses = ObjectiveStatus::cases();

        return view('pages.specialized-educational-support.peis.show', compact('pei', 'student', 'statuses'));
    }

    public function create(Student $student)
    {
        $courses = Course::orderBy('name')->pluck('name', 'id');
        $disciplines = Discipline::orderBy('name')->pluck('name', 'id');
        $currentContext = $student->contexts()->where('is_current', true)->first();
        $semester = Semester::current();
        
        return view('pages.specialized-educational-support.peis.create', compact('student', 'currentContext', 'courses', 'disciplines', 'semester'));
    }

    public function store(PeiRequest $request)
    {
        $pei = $this->service->create($request->validated());

        return redirect()
            ->route('specialized-educational-support.pei.show', $pei)
            ->with('success', 'PEI gerado com sucesso. Agora você pode adicionar os objetivos e metodologias.');
    }

    public function edit(Pei $pei)
    {
        $courses = Course::orderBy('name')->pluck('name', 'id');
        $disciplines = Discipline::orderBy('name')->pluck('name', 'id');
        $student = $pei->student;

        return view('pages.specialized-educational-support.peis.edit', compact('pei', 'student', 'courses', 'disciplines'));
    }

    public function update(Pei $pei, PeiRequest $request)
    {
        $this->service->update($pei, $request->validated());

        return redirect()
            ->route('specialized-educational-support.pei.show', $pei)
            ->with('success', 'Dados básicos do PEI atualizados com sucesso.');
    }

    public function destroy(Pei $pei)
    {
        $student = $pei->student;
        $this->service->delete($pei);

        return redirect()
            ->route('specialized-educational-support.pei.index', $student)
            ->with('success', 'PEI removido com sucesso.');
    }

    public function finish(Pei $pei)
    {
        $this->service->finish($pei);

        return redirect()
            ->route('specialized-educational-support.pei.show', $pei)
            ->with('success', 'PEI finalizado com sucesso.');
    }

    // --- Métodos para Tabelas Auxiliares (Chamados via modal ou formulário na tela Show) ---

    public function showObjective(SpecificObjective $specific_objective)
    {
        $pei = $specific_objective->pei;
        $statuses = ObjectiveStatus::labels();

        return view(
            'pages.specialized-educational-support.peis.objectives.show', 
            compact('specific_objective', 'pei', 'statuses')
        );
    }

    public function createObjective(Pei $pei)
    {
        $student = $pei->student;
        $statuses = ObjectiveStatus::labels();

        return view(
            'pages.specialized-educational-support.peis.objectives.create',
            compact('pei', 'student', 'statuses')
        );
    }

    public function storeObjective(Pei $pei, SpecificObjectiveRequest $request)
    {
        $this->service->addObjective($pei, $request->validated());

        return redirect()
            ->route('specialized-educational-support.pei.show', $pei)
            ->with('success', 'Objetivo específico adicionado com sucesso.');
    } 

    public function editObjective(SpecificObjective $specific_objective)
    {
        $statuses = ObjectiveStatus::labels();
        $pei = $specific_objective->pei;

        return view(
            'pages.specialized-educational-support.peis.objectives.edit',
            compact('specific_objective', 'statuses', 'pei')
        );
    }

    public function updateObjective(SpecificObjective $specific_objective, SpecificObjectiveRequest $request)
    {

        $pei = $specific_objective->pei;
        $this->service->updateObjective($specific_objective, $request->validated());

        return redirect()
            ->route('specialized-educational-support.pei.show', $pei)
            ->with('success', 'Dados do objetivo atualizados com sucesso.');
    }

    public function destroyObjective(SpecificObjective $specific_objective)
    {
        $this->service->deleteObjective($specific_objective);

        return back()->with('success', 'Objetivo removido.');
    }

    // Tela para criar Conteúdo Programático

    public function showContent(ContentProgrammatic $content_programmatic)
    {
        $pei = $content_programmatic->pei;

        return view(
            'pages.specialized-educational-support.peis.contents.show', 
            compact('content_programmatic', 'pei')
        );
    }

    public function editContent(ContentProgrammatic $content_programmatic)
    {
        $pei = $content_programmatic->pei;

        return view(
            'pages.specialized-educational-support.peis.contents.edit',
            compact('content_programmatic', 'pei')
        );
    }

    public function updateContent(ContentProgrammatic $content_programmatic, ContentProgrammaticRequest $request)
    {
        $pei = $content_programmatic->pei;
        $this->service->updateContent($content_programmatic, $request->validated());

        return redirect()
            ->route('specialized-educational-support.pei.show', $pei)
            ->with('success', 'Conteúdo programático atualizado com sucesso.');
    }

    public function createContent(Pei $pei)
    {
        $student = $pei->student;

        return view(
            'pages.specialized-educational-support.peis.contents.create',
            compact('pei', 'student')
        );
    }

    public function storeContent(Pei $pei, ContentProgrammaticRequest $request)
    {
        $this->service->addContent($pei, $request->validated());

        return redirect()
            ->route('specialized-educational-support.pei.show', $pei)
            ->with('success', 'Conteúdo programático adicionado com sucesso.');
    }

    public function destroyContent(ContentProgrammatic $content_programmatic)
    {
        $this->service->deleteContent($content_programmatic);

        return back()->with('success', 'Conteúdo removido.');
    }

    // Tela para criar Metodologia

    public function showMethodology(Methodology $methodology)
    {
        $pei = $methodology->pei;

        return view(
            'pages.specialized-educational-support.peis.methodologies.show', 
            compact('methodology', 'pei')
        );
    }

    public function editMethodology(Methodology $methodology)
    {
        $pei = $methodology->pei;

        return view(
            'pages.specialized-educational-support.peis.methodologies.edit',
            compact('methodology', 'pei')
        );
    }

    public function updateMethodology(Methodology $methodology, MethodologyRequest $request)
    {
        $pei = $methodology->pei;
        $this->service->updateMethodology($methodology, $request->validated());

        return redirect()
            ->route('specialized-educational-support.pei.show', $pei)
            ->with('success', 'Metodologia atualizada com sucesso.');
    }
    
    public function createMethodology(Pei $pei)
    {
        $student = $pei->student;

        return view(
            'pages.specialized-educational-support.peis.methodologies.create',
            compact('pei', 'student')
        );
    }

    public function storeMethodology(Pei $pei, MethodologyRequest $request)
    {
        $this->service->addMethodology($pei, $request->validated());

        return redirect()
            ->route('specialized-educational-support.pei.show', $pei)
            ->with('success', 'Metodologia adicionada com sucesso.');
    }
    public function destroyMethodology(Methodology $methodology)
    {
        $this->service->deleteMethodology($methodology);

        return back()->with('success', 'Metodologia removida.');
    }

    public function generatePdf(Pei $pei)
    {
        $pei->load(['student.person', 'studentContext', 'specificObjectives', 'contentProgrammatic', 'methodologies']);
        
        $pdf = Pdf::loadView('pages.specialized-educational-support.peis.pdf', compact('pei'))
                ->setPaper('a4', 'portrait');

        return $pdf->stream("PEI_{$pei->student->person->name}_{$pei->discipline->name}.pdf");
    }
}