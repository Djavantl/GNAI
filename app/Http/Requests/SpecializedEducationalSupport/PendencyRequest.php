<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;

class PendencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'assigned_to' => [
                'required',
                'integer',
                'exists:professionals,id',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'priority' => [
                'required',
                'in:urgent,high,medium,low',
            ],

            'due_date' => [
                'nullable',
                'date',
                'after_or_equal:today',
            ],

            'is_completed' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'assigned_to.required' => 'A responsible professional must be selected.',
            'assigned_to.exists'   => 'The selected professional is invalid.',

            'title.required' => 'The title field is required.',
            'title.max'      => 'The title may not be greater than 255 characters.',

            'priority.in' => 'Invalid priority level selected.',

            'due_date.after_or_equal' =>
                'The due date must be today or a future date.',
        ];
    }

}
