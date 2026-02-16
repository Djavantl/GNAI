<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\{StudentDocument, Student};
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{DB, Storage};
use Illuminate\Support\Facades\Auth;
use App\Enums\SpecializedEducationalSupport\StudentDocumentType;

class StudentDocumentService
{
    protected string $disk = 'local';
    protected $semesterService;

    public function __construct(SemesterService $semesterService)
    {
        $this->semesterService = $semesterService;
    }

    public function index(Student $student)
    {
        return $student->documents()->latest()->get();
    }

    public function create(Student $student, array $data): StudentDocument
    {
        return DB::transaction(function () use ($student, $data) {
            $semester = $this->semesterService->getCurrent(); 
            
            if (!$semester) {
                throw new \Exception('Não existe semestre atual configurado no sistema.');
            }

            // Prepara os dados iniciais
            $data['student_id']  = $student->id;
            $data['semester_id']    = $semester->id;
            $data['uploaded_by'] = Auth::id();
            $data['version']     = $data['version'] ?? $this->nextVersion($student->id, $data['type']);

            // Processa o arquivo
            if (isset($data['file'])) {
                $data = array_merge($data, $this->prepareFileData($student->id, $data['type'], $data['file']));
            }

            return StudentDocument::create($data);
        });
    }

    public function update(StudentDocument $document, array $data): StudentDocument
    {
        return DB::transaction(function () use ($document, $data) {
            if (!empty($data['file']) && $data['file'] instanceof UploadedFile) {
                // Substituição física
                $this->deletePhysicalFile($document->file_path);
                
                // Merge dos metadados e incremento de versão
                $data = array_merge($data, $this->prepareFileData($document->student_id, $data['type'] ?? $document->type, $data['file']));
                $data['version'] = $document->version + 1;
            }

            $document->update($data);
            return $document->refresh();
        });
    }

    public function delete(StudentDocument $document): void
    {
        DB::transaction(function () use ($document) {
            $this->deletePhysicalFile($document->file_path);
            $document->delete();
        });
    }

    public function download(StudentDocument $document)
    {
        return Storage::disk($this->disk)->download($document->file_path, $document->original_name);
    }

    /**
     * Auxiliar para extrair dados do arquivo e gerar o path
     */
    private function prepareFileData(int $studentId, string $type, UploadedFile $file): array
    {
        return [
            'file_path'     => $file->store("student_documents/{$studentId}/{$type}", $this->disk),
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'file_size'     => $file->getSize(),
        ];
    }

    protected function deletePhysicalFile(?string $path): void
    {
        if ($path && Storage::disk($this->disk)->exists($path)) {
            Storage::disk($this->disk)->delete($path);
        }
    }

    protected function nextVersion(int $studentId, string $type): int
    {
        return (int) StudentDocument::where('student_id', $studentId)
            ->where('type', $type)
            ->max('version') + 1;
    }
}