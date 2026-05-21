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
}
