<?php

namespace App\Http\Requests\Interview;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInterviewRequest extends FormRequest
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
            'date' => 'sometimes|date',
            'time' => 'sometimes',
            'interviewer' => 'sometimes|string',
            'type' => 'sometimes|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'date.date'          => 'Please provide a valid date for the interview.',
            'interviewer.string' => 'Interviewer name must be a valid text string.',
            'type.string'        => 'Interview type must be a valid text string.',
        ];
    }
}
