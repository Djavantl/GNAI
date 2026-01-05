<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssistiveTechnologyStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:assistive_technology_statuses,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }
}
