<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\UI\Presenters;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\ContextEvaluationType;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\DeficiencySeverity;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\StudentStatus;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Person;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Shared\Infrastructure\Security\RichTextSanitizer;
use Illuminate\Http\Request;

/** Presentation-only values that Blade previously read through accessors and Gates. */
final class StudentPagePresenter
{
    public function shared(Request $request): array
    {
        $abilities = [
            'student.view', 'student.create', 'student.update', 'student.delete',
            'student-course.view', 'student-course.create', 'student-course.update', 'student-course.delete',
            'student-deficiency.view', 'student-deficiency.create', 'student-deficiency.update', 'student-deficiency.delete',
            'guardian.view', 'guardian.create', 'guardian.update', 'guardian.delete',
            'student-context.view', 'student-context.create', 'student-context.update', 'student-context.delete',
            'pei.view', 'pei.create', 'pei.update', 'pei.delete',
            'student-document.view', 'student-document.create', 'student-document.update', 'student-document.delete',
            'aee-record.view', 'aee-record.view-all', 'aee-record.view-own', 'aee-record.update', 'aee-record.delete',
            'pedagogical-record.view', 'pedagogical-record.view-all', 'pedagogical-record.view-own',
            'pedagogical-record.update', 'pedagogical-record.delete',
        ];

        return [
            'permissions' => collect($abilities)->mapWithKeys(
                fn (string $ability): array => [$ability => $request->user()?->can($ability) ?? false],
            )->all(),
            'studentStatuses' => collect(StudentStatus::cases())->mapWithKeys(
                fn (StudentStatus $status): array => [$status->value => $status->label()],
            )->all(),
            'statusColors' => collect(StudentStatus::cases())->mapWithKeys(
                fn (StudentStatus $status): array => [$status->value => $status->color()],
            )->all(),
        ];
    }

    public function student(Student $student, Request $request): array
    {
        $data = $student->toArray();
        $data['person'] = $this->person($student->person);
        $data['entry_date'] = $student->entry_date?->toDateString();

        // Do not serialize sections that the legacy screen hid through @can.
        foreach ([
            'guardians' => 'guardian.view',
            'documents' => 'student-document.view',
            'contexts' => 'student-context.view',
            'current_context' => 'student-context.view',
            'deficiencies' => 'student-deficiency.view',
            'peis' => 'pei.view',
        ] as $key => $ability) {
            if (! $request->user()?->can($ability)) {
                unset($data[$key]);
            }
        }

        if (isset($data['guardians'])) {
            $data['guardians'] = $student->guardians->map(fn ($guardian): array => [
                ...$guardian->toArray(),
                'person' => $this->person($guardian->person),
                'relationship_label' => $guardian->relationshipLabel(),
            ])->all();
        }
        if (isset($data['documents'])) {
            $data['documents'] = $student->documents->map(fn ($document): array => [
                ...$document->toArray(),
                'type_label' => $document->type->label(),
                'semester_label' => $document->semester?->label,
            ])->all();
        }
        if (isset($data['contexts'])) {
            $data['contexts'] = $student->contexts->map(function ($context): array {
                $evaluationType = ContextEvaluationType::tryFrom(
                    (string) $context->evaluation_type,
                );

                return [
                    ...$context->toArray(),
                    'semester_label' => $context->semester?->label,
                    'evaluation_type_label' => $evaluationType?->label()
                        ?? (string) $context->evaluation_type,
                    'can_manage' => $context->canBeManagedByCurrentUser(),
                ];
            })->all();
        }
        if (isset($data['current_context'])) {
            $data['current_context']['semester_label'] = $student->currentContext->semester?->label;
        }
        if (isset($data['deficiencies'])) {
            $data['deficiencies'] = $student->deficiencies->map(fn ($deficiency): array => [
                ...$deficiency->toArray(),
                'student_deficiency_id' => (int) $deficiency->pivot->getKey(),
                'severity_label' => DeficiencySeverity::tryFrom($deficiency->pivot->severity ?? '')?->label(),
                'severity_color' => DeficiencySeverity::tryFrom($deficiency->pivot->severity ?? '')?->color(),
            ])->all();
        }
        if (isset($data['peis'])) {
            $data['peis'] = $student->peis->map(fn ($pei): array => [
                ...$pei->toArray(),
                'semester_label' => $pei->semester?->label,
                'teacher_names' => $pei->peiDisciplines->pluck('teacher.person.name')->filter()->unique()->values()->all(),
            ])->all();
        }

        return $data;
    }

    private function person(Person $person): array
    {
        return [
            ...$person->toArray(),
            'photo_url' => $person->photo_url,
            'gender_label' => $person->gender_label,
            'birth_date' => $person->birth_date?->toDateString(),
            'age' => $person->birth_date?->age,
            'address_html' => RichTextSanitizer::sanitize($person->address ?? '', decodeEntities: false),
        ];
    }
}
