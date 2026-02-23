<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SpecializedEducationalSupport\StudentContextRequest;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\StudentContext;
use App\Services\SpecializedEducationalSupport\StudentContextService;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SpecializedEducationalSupport\Professional;
use Throwable;

class StudentContextController extends Controller
{
    protected StudentContextService $service;

    public function __construct(StudentContextService $service)
    {
        $this->service = $service;
    }

    public function index(Student $student, Request $request)
    {
        $contexts = $this->service->getByStudent($student, $request->all());

        $semesters = \App\Models\SpecializedEducationalSupport\Semester::query()
            ->orderByDesc('year')
            ->orderByDesc('term')
            ->get()
            ->pluck('label', 'id')
            ->prepend('Semestre (Todos)', '');

        if ($request->ajax()) {
            return view(
                'pages.specialized-educational-support.student-context.partials.table',
                compact('contexts', 'student')
            )->render();
        }

        return view(
            'pages.specialized-educational-support.student-context.index',
            compact('contexts', 'student', 'semesters')
        );
    }

    public function show(StudentContext $studentContext)
    {
        $student = $studentContext->student;
        $student->load('deficiencies');
        $studentContext = $this->service->show($studentContext);
        $deficiencies = $student->deficiencies;
        return view('pages.specialized-educational-support.student-context.show', compact('student', 'studentContext', 'deficiencies'));
    }

    public function showCurrent(Student $student)
    {
        $context = $this->service->showCurrent($student);

        $student->load('deficiencies');

        return view(
            'pages.specialized-educational-support.student-context.show',
            compact('student', 'context')
        );
    }

    public function create(Student $student)
    {
        $exists = StudentContext::where('student_id', $student->id)->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->with('error', 'Este aluno já possui contexto. Use "Nova Versão".');
        }
        $student->load('deficiencies');
        $deficiencies = $student->deficiencies;
        $professionals = Professional::with('person')->get();

        return view(
            'pages.specialized-educational-support.student-context.create',
            compact('student', 'deficiencies', 'professionals')
        );
    }

    public function store(Student $student, StudentContextRequest $request)
    {
        try {
            $this->service->create($student, $request->validated());

            return redirect()
                ->route('specialized-educational-support.student-context.index', $student)
                ->with('success', 'Contexto do aluno cadastrado com sucesso.');

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao criar contexto.');
        }
    }

    public function edit(StudentContext $studentContext)
    {
        if(!$studentContext->is_current){
            return redirect()
                ->route('specialized-educational-support.student-context.show', $studentContext)
                ->with('error', 'Não é possível editar um contexto que não é atual.');
        }

        $student = $studentContext->student;
        $student->load('deficiencies');
        $deficiencies = $student->deficiencies;
        $professionals = Professional::with('person')->get();

        return view(
            'pages.specialized-educational-support.student-context.edit',
            compact(
                'studentContext',
                'student',
                'deficiencies',
                'professionals',
            )
        );
    }

    public function update(StudentContext $studentContext, StudentContextRequest $request)
    {
        try {
            $studentContext = $this->service->update($studentContext, $request->validated());

            return redirect()
                ->route('specialized-educational-support.student-context.show', $studentContext)
                ->with('success', 'Contexto salvo com sucesso.');

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao atualizar contexto.');
        }
    }

    public function makeNewVersion(Student $student)
    {
        try {
            $studentContext = $this->service->makeNewVersion($student);

            $student->load('deficiencies');
            $professionals = Professional::with('person')->get();

            return view(
                'pages.specialized-educational-support.student-context.version',
                compact('studentContext', 'student', 'professionals')
            );

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao gerar nova versão.');
        }
    }

    public function storeNewVersion(Student $student, StudentContextRequest $request)
    {
        try {
            $newContext = $this->service->createNewVersion(
                $student,
                $request->validated()
            );

            return redirect()
                ->route('specialized-educational-support.student-context.show', $newContext)
                ->with('success', 'Nova versão criada e definida como atual.');

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao salvar nova versão.');
        }
    }

    public function restoreVersion(StudentContext $studentContext)
    {
        try {
            $newContext = $this->service->restoreVersion($studentContext);

            return redirect()
                ->route('specialized-educational-support.student-context.show', $newContext)
                ->with('success', 'Contexto restaurado e definido como atual.');

        } catch (Throwable $e) {
           return $this->handleException($e, 'Erro ao restaurar versão.');
        }
    }

    public function destroy(StudentContext $studentContext)
    {
        try {
            $student = $studentContext->student;

            $this->service->delete($studentContext);

            return redirect()
                ->route('specialized-educational-support.student-context.index', $student)
                ->with('success', 'Contexto do aluno removido com sucesso.');

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao excluir contexto.');
        }
    }

    public function generatePdf($studentContext)
    {
        try {
            $context = StudentContext::with([
                'student.person',
                'student.deficiencies'
            ])->findOrFail($studentContext);

            $student = $context->student;

            $pdf = Pdf::loadView(
                    'pages.specialized-educational-support.student-context.pdf',
                    compact('context', 'student')
                )
                ->setPaper('a4', 'portrait')
                ->setOption(['enable_php' => true]);

            return $pdf->stream("Contexto_{$student->person->name}.pdf");

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao gerar pdf.');
        }
    }
}
