<?php

namespace App\Http\Requests\Application;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
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
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'cv' => 'required|file|mimes:pdf|max:2048'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'full_name.required' => 'Candidate full name is required.',
            'full_name.string'   => 'Full name must be a valid text string.',
            'full_name.max'      => 'Full name must not exceed 255 characters.',

            'email.required' => 'Candidate email is required.',
            'email.email'    => 'Please enter a valid email address.',
            'email.max'      => 'Email address must not exceed 255 characters.',

            'phone_number.required' => 'Phone number is required.',
            'phone_number.string'   => 'Phone number must be a valid text string.',
            'phone_number.max'      => 'Phone number must not exceed 20 characters.',

            'cv.required' => 'A CV file is required to apply.',
            'cv.file'     => 'The CV must be an uploaded file.',
            'cv.mimes'    => 'CV must be a PDF file.',
            'cv.max'      => 'CV file size must not exceed 2 MB.',
        ];
    }
}
