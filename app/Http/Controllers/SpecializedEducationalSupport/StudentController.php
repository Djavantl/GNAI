<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpecializedEducationalSupport\StudentRequest;
use App\Models\SpecializedEducationalSupport\Person;
use App\Models\SpecializedEducationalSupport\Student;
use App\Services\SpecializedEducationalSupport\StudentService;
use App\Support\PdfPageNumberer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Throwable;

class StudentController extends Controller
{
    protected StudentService $service;

    public function __construct(StudentService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            $students = $this->service->index($request->all());
            $semesters = $this->semesters();

            if ($request->ajax()) {
                return view(
                    'pages.specialized-educational-support.students.partials.table',
                    compact('students', 'semesters')
                )->render();
            }

            return view(
                'pages.specialized-educational-support.students.index',
                compact('students', 'semesters')
            );

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao listar alunos.');
        }
    }

    public function show(Student $student)
    {
        try {
            $student = $this->service->show($student);
            $sessionEvaluations = $this->service->studentSessionEvaluations($student, 5);
            $pedagogicalRecords = $this->service->studentPedagogicalRecords($student, 5);

            return view(
                'pages.specialized-educational-support.students.show',
                compact('student', 'sessionEvaluations', 'pedagogicalRecords')
            );

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao exibir aluno.');
        }
    }

    public function create()
    {
        $people = Person::orderBy('name')->get();

        return view(
            'pages.specialized-educational-support.students.create',
            compact('people')
        );
    }

    public function store(StudentRequest $request)
    {
        try {
            $student = $this->service->create($request->validated());

            return redirect()
                ->route('specialized-educational-support.students.show', $student)
                ->with('success', 'Aluno cadastrado com sucesso.');

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao cadastrar aluno.');
        }
    }

    public function edit(Student $student)
    {
        $people = Person::orderBy('name')->get();

        return view(
            'pages.specialized-educational-support.students.edit',
            compact('student', 'people')
        );
    }

    public function update(StudentRequest $request, Student $student)
    {
        try {
            $student = $this->service->update($student, $request->validated());

            return redirect()
                ->route('specialized-educational-support.students.show', $student)
                ->with('success', 'Aluno atualizado com sucesso.');

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao atualizar aluno.');
        }
    }

    public function destroy(Student $student)
    {
        try {
            $this->service->delete($student);

            return redirect()
                ->route('specialized-educational-support.students.index')
                ->with('success', 'Aluno removido com sucesso.');

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao remover aluno.');
        }
    }

    public function pdf(Student $student)
    {
        try {
            $student = $this->service->pdfData($student);

            $pdf = Pdf::loadView(
                'pages.specialized-educational-support.students.pdf',
                compact('student')
            );

            PdfPageNumberer::apply($pdf);

            return $pdf->stream(
                "ficha-aluno-{$student->registration}.pdf"
            );

        } catch (Throwable $e) {
            throw $e;
        }
    }
}
