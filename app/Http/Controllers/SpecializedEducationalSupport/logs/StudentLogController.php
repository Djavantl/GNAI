<?php

namespace App\Http\Controllers\SpecializedEducationalSupport\Logs;

use App\Http\Controllers\Controller;
use App\Models\SpecializedEducationalSupport\{
    Student, Person, StudentDeficiencies, StudentDocument, StudentCourse, StudentContext
};
use App\Models\AuditLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Relations\Relation;

class StudentLogController extends Controller
{
    public function index(Student $student)
    {
        $logs = $this->getConsolidatedLogs($student)->paginate(30); // Aumentado para 30
        $fieldLabels = $this->getAllLabels();

        return view('pages.specialized-educational-support.students.logs.index', compact('student', 'logs', 'fieldLabels'));
    }

    public function generatePdf(Student $student)
    {
        $logs = $this->getConsolidatedLogs($student)->get();
        $fieldLabels = $this->getAllLabels();

        $pdf = Pdf::loadView(
            'pages.specialized-educational-support.students.logs.logs-pdf',
            compact('student', 'logs', 'fieldLabels')
        )
            ->setPaper('a4', 'portrait')
            ->setOption(['enable_php' => true, 'isHtml5ParserEnabled' => true]);

        $fileName = "Historico_Completo_AEE_" . str_replace(' ', '_', $student->person->name) . ".pdf";
        return $pdf->stream($fileName);
    }

    private function getConsolidatedLogs(Student $student)
    {
        $studentMorph = $student->getMorphClass();
        $personMorph  = (new Person())->getMorphClass();

        return AuditLog::where(function($query) use ($student, $studentMorph, $personMorph) {
            // Logs diretos no aluno
            $query->where(function($q) use ($studentMorph, $student) {
                $q->where('auditable_type', $studentMorph)
                ->where('auditable_id', $student->id);
            })
            // Logs diretos na person vinculada
            ->orWhere(function($q) use ($personMorph, $student) {
                $q->where('auditable_type', $personMorph)
                ->where('auditable_id', $student->person_id);
            })
            // Qualquer log que referencie esse aluno ou essa pessoa dentro de old_values/new_values
            ->orWhere(function($q) use ($student) {
                $q->where(function($sub) use ($student) {
                    $sub->where('new_values->student_id', $student->id)
                        ->orWhere('old_values->student_id', $student->id)
                        ->orWhere('new_values->person_id', $student->person_id)
                        ->orWhere('old_values->person_id', $student->person_id);
                });
            });
        })
        ->with('user')
        ->latest();
    }

    private function getAllLabels(): array
    {
        return array_merge(
            Student::getAuditLabels(),
            Person::getAuditLabels(),
            StudentDeficiencies::getAuditLabels(),
            StudentDocument::getAuditLabels(),
            StudentCourse::getAuditLabels(),
            StudentContext::getAuditLabels()
        );
    }
}