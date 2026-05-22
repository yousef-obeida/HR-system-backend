<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['admin', 'hr'])],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'User name is required.',
            'name.string'   => 'User name must be a valid text string.',
            'name.max'      => 'User name must not exceed 255 characters.',

            'email.required' => 'Email address is required.',
            'email.string'   => 'Email must be a valid text string.',
            'email.email'    => 'Please enter a valid email address.',
            'email.max'      => 'Email address must not exceed 255 characters.',
            'email.unique'   => 'This email address is already registered.',

            'password.required' => 'Password is required.',
            'password.string'   => 'Password must be a valid text string.',
            'password.min'      => 'Password must be at least 8 characters long.',

            'role.required' => 'User role is required.',
            'role.in'       => 'Role must be either "admin" or "hr".',
        ];
    }
}
