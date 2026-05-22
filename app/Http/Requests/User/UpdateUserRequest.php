<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user') ?? $this->route('id'); // Assuming the route parameter is 'user' or 'id'
        return [
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId, 'id')],
            'password' => 'sometimes|string|min:8',
            'role' => ['sometimes', Rule::in(['admin', 'hr'])],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.string' => 'User name must be a valid text string.',
            'name.max'    => 'User name must not exceed 255 characters.',

            'email.string' => 'Email must be a valid text string.',
            'email.email'  => 'Please enter a valid email address.',
            'email.max'    => 'Email address must not exceed 255 characters.',
            'email.unique' => 'This email address is already taken by another user.',

            'password.string' => 'Password must be a valid text string.',
            'password.min'    => 'Password must be at least 8 characters long.',

            'role.in' => 'Role must be either "admin" or "hr".',
        ];
    }
}
