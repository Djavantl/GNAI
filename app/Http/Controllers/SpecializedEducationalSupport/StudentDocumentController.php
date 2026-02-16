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


class StudentDocumentController extends Controller
{
    protected StudentDocumentService $service;

    public function __construct(StudentDocumentService $service)
    {
        $this->service = $service;
    }

    public function index(Student $student)
    {
        $documents = $this->service->index($student);
        return view('pages.specialized-educational-support.student-documents.index', compact('student', 'documents'));
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
        $semester = Semester::current();
        $types = StudentDocumentType::labels();

        return view('pages.specialized-educational-support.student-documents.create', compact('student', 'types', 'semester'));
    }

    public function store(StudentDocumentRequest $request, Student $student)
    {
        $this->service->create($student, $request->validated());

        return redirect()
            ->route('specialized-educational-support.student-documents.index', $student)
            ->with('success', 'Documento enviado com sucesso.');
    }

    public function edit(StudentDocument $studentDocument)
    {
        $types = StudentDocumentType::labels();
        $student = $studentDocument->student;
        $semester = Semester::current();

        return view('pages.specialized-educational-support.student-documents.edit', compact('studentDocument', 'student', 'types', 'semester'));
    }

    public function update(StudentDocumentRequest $request, StudentDocument $studentDocument)
    {
        $this->service->update($studentDocument, $request->validated());

        return redirect()
            ->route('specialized-educational-support.student-documents.index', $studentDocument->student_id)
            ->with('success', 'Documento atualizado com sucesso.');
    }

    public function destroy(StudentDocument $studentDocument)
    {
        $studentId = $studentDocument->student_id;
        $this->service->delete($studentDocument);

        return redirect()
            ->route('specialized-educational-support.student-documents.index', $studentId)
            ->with('success', 'Documento removido com sucesso.');
    }

    public function download(StudentDocument $studentDocument)
    {
        return $this->service->download($studentDocument);
    }
}