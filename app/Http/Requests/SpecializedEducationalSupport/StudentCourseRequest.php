<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $student = $this->route('student');
        $studentId = $student instanceof \App\Models\SpecializedEducationalSupport\Student
            ? $student->id
            : $student;
        return [
            
            'course_id' => [
                'required',
                Rule::unique('student_courses')
                    ->where('student_id', $studentId),
            ],
            'academic_year'=> 'required|digits:4',
            'is_current'   => 'boolean',
        ];
    }
}
