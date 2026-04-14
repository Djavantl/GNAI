<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpecializedEducationalSupport\StudentDocumentRequest;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\StudentDocument;
use App\Services\SpecializedEducationalSupport\StudentDocumentService;
use App\Enums\SpecializedEducationalSupport\StudentDocumentType;
use App\Models\SpecializedEducationalSupport\Semester;
use Illuminate\Support\Facades\Response; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Throwable;

class StudentDocumentController extends Controller
{
    protected StudentDocumentService $service;

    public function __construct(StudentDocumentService $service)
    {
        $this->service = $service;
    }

    public function index(Student $student, Request $request)
    {
        try {
            $documents = $this->service->getByStudent($student, $request->all());

            $semesters = Semester::query()
                ->orderByDesc('year')
                ->orderByDesc('term')
                ->get()
                ->pluck('label', 'id')
                ->prepend('Semestre (Todos)', '');

            $types = collect(StudentDocumentType::labels())
                ->prepend('Tipo (Todos)', '')
                ->toArray();

            $versions = StudentDocument::where('student_id', $student->id)
                ->orderByDesc('version')
                ->pluck('version', 'version')
                ->prepend('Versão (Todas)', '');

            if ($request->ajax()) {
                return view(
                    'pages.specialized-educational-support.student-documents.partials.table',
                    compact('documents', 'student')
                )->render();
            }

            return view(
                'pages.specialized-educational-support.student-documents.index',
                compact('documents', 'student', 'semesters', 'versions', 'types')
            );

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao listar documentos do aluno.');
        }
    }

    public function show(StudentDocument $studentDocument)
    {
        if (!Storage::disk('local')->exists($studentDocument->file_path)) {
            abort(404);
        }

        $file = Storage::disk('local')->get($studentDocument->file_path);
        $type = Storage::disk('local')->mimeType($studentDocument->file_path);

        return Response::make($file, 200, [
            'Content-Type' => $type,
            'Content-Disposition' => 'inline; filename="'.$studentDocument->original_name.'"'
        ]);
    }

    public function create(Student $student)
    {
        $student->ensureIsActive();

        $semester = Semester::current();
        $types = StudentDocumentType::labels();

        return view(
            'pages.specialized-educational-support.student-documents.create',
            compact('student', 'types', 'semester')
        );
    }

    public function store(StudentDocumentRequest $request, Student $student)
    {
        try {
            $student->ensureIsActive();

            $this->service->create($student, $request->validated());

            return redirect()
                ->route('specialized-educational-support.student-documents.index', $student)
                ->with('success', 'Documento enviado com sucesso.');

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao enviar documento.');
        }
    }

    public function edit(StudentDocument $studentDocument)
    {
        $student = $studentDocument->student;
        $student->ensureIsActive();

        $types = StudentDocumentType::labels();
        $semester = Semester::current();

        return view(
            'pages.specialized-educational-support.student-documents.edit',
            compact('studentDocument', 'student', 'types', 'semester')
        );
    }

    public function update(StudentDocumentRequest $request, StudentDocument $studentDocument)
    {
        try {
            $student = $studentDocument->student;
            $student->ensureIsActive();
            
            $this->service->update($studentDocument, $request->validated());

            return redirect()
                ->route('specialized-educational-support.student-documents.index', $studentDocument->student_id)
                ->with('success', 'Documento atualizado com sucesso.');

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao atualizar documento.');
        }
    }

    public function destroy(StudentDocument $studentDocument)
    {
        try {
            $studentId = $studentDocument->student_id;

            $this->service->delete($studentDocument);

            return redirect()
                ->route('specialized-educational-support.student-documents.index', $studentId)
                ->with('success', 'Documento removido com sucesso.');

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao remover documento.');
        }
    }

    public function download(StudentDocument $studentDocument)
    {
        try {
            return $this->service->download($studentDocument);

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao realizar download do documento.');
        }
    }
}