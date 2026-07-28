<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\StudentDocuments;

use App\Domains\SpecializedEducationalSupport\Application\Data\StudentDocuments\ListStudentDocumentsData;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentDocument;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListStudentDocumentsQuery
{
    /**
     * @return LengthAwarePaginator<int, StudentDocument>
     */
    public function execute(Student $student, ListStudentDocumentsData $filters): LengthAwarePaginator
    {
        $query = StudentDocument::query()
            ->where('student_id', $student->getKey())
            ->with(['semester', 'uploader']);

        if (filled($filters->title)) {
            $query->where('title', 'like', '%'.trim($filters->title).'%');
        }

        if ($filters->type !== null) {
            $query->where('type', $filters->type->value);
        }

        if ($filters->semesterId !== null) {
            $query->where('semester_id', $filters->semesterId);
        }

        return $query
            ->orderByDesc('created_at')
            ->paginate($filters->perPage)
            ->withQueryString();
    }
}
