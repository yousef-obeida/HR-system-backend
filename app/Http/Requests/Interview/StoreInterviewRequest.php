<?php

namespace App\Http\Requests\Interview;

use Illuminate\Foundation\Http\FormRequest;

class StoreInterviewRequest extends FormRequest
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
            'application_id' => 'required|exists:applications,id',
            'date' => 'required|date',
            'time' => 'required',
            'interviewer' => 'required|string',
            'type' => 'required|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'application_id.required' => 'The application ID is required to schedule an interview.',
            'application_id.exists'   => 'The selected application does not exist.',

            'date.required' => 'Interview date is required.',
            'date.date'     => 'Please provide a valid date for the interview.',

            'time.required' => 'Interview time is required.',

            'interviewer.required' => 'Interviewer name is required.',
            'interviewer.string'   => 'Interviewer name must be a valid text string.',

            'type.required' => 'Interview type is required (e.g., Technical, HR).',
            'type.string'   => 'Interview type must be a valid text string.',
        ];
    }
}
