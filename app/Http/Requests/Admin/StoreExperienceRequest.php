<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreExperienceRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->hasRole(['admin', 'editor']);
    }

    public function rules()
    {
        return [
            'company' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'description' => 'required|string',
            'technologies' => 'nullable|array',
            'technologies.*' => 'string|max:100',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0'
        ];
    }

    public function messages()
    {
        return [
            'company.required' => 'Company name is required.',
            'position.required' => 'Position title is required.',
            'start_date.required' => 'Start date is required.',
            'end_date.after' => 'End date must be after start date.',
            'description.required' => 'Job description is required.'
        ];
    }
}
