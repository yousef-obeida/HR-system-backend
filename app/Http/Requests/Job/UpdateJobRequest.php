<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobRequest extends FormRequest
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
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'requirments' => 'sometimes|required|string',
            'status' => 'sometimes|required|in:open,closed',
            'Location' => 'sometimes|required|in:onsite,remote,hybrid',
            'salary' => 'nullable|integer'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.string' => 'Job title must be a valid text string.',
            'title.max'    => 'Job title must not exceed 255 characters.',

            'description.string' => 'Job description must be a valid text string.',

            'requirments.string' => 'Job requirements must be a valid text string.',

            'status.in' => 'Job status must be either "open" or "closed".',

            'Location.in' => 'Job location must be one of: onsite, remote, or hybrid.',

            'salary.integer' => 'Salary must be a valid number.',
        ];
    }
}
