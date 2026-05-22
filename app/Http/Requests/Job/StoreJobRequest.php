<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirments' => 'required|string',
            'status' => 'required|in:open,closed',
            'Location' => 'required|in:onsite,remote,hybrid',
            'salary' => 'nullable|integer'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Job title is required.',
            'title.string'   => 'Job title must be a valid text string.',
            'title.max'      => 'Job title must not exceed 255 characters.',

            'description.required' => 'Job description is required.',
            'description.string'   => 'Job description must be a valid text string.',

            'requirments.required' => 'Job requirements are required.',
            'requirments.string'   => 'Job requirements must be a valid text string.',

            'status.required' => 'Job status is required.',
            'status.in'       => 'Job status must be either "open" or "closed".',

            'Location.required' => 'Job location type is required.',
            'Location.in'       => 'Job location must be one of: onsite, remote, or hybrid.',

            'salary.integer' => 'Salary must be a valid number.',
        ];
    }
}
