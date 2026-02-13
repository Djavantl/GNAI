<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;

class PeiRequest extends FormRequest
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
        return [
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'discipline_id' => 'required|exists:disciplines,id',
            'teacher_name' => 'required|string|max:255',
            'student_context_id' => 'required|exists:student_contexts,id',
        ];
    }
}
