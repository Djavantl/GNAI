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
        $isProfessor = auth()->user() && auth()->user()->teacher_id !== null;
        
        return [
            'student_id' => 'required|exists:students,id',
            'discipline_id' => 'required|exists:disciplines,id',
            'teacher_name' => $isProfessor ? 'nullable|string|max:255' : 'required|string|max:255',
            'teacher_id' => 'nullable|exists:teachers,id',
            'course_id' => 'nullable|exists:courses,id',
            'student_context_id' => 'nullable|exists:student_contexts,id',
        ];
    }
}
