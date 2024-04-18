<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class LoginRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', Rule::exists('users', 'email')],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.exists' => __('The provided credentials do not match our records.'),
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    public function bodyParameters(): array
    {
        return [
            'email' => [
                'example' => 'admin@testing.com',
            ],
            'password' => [
                'example' => 'Password123!',
            ],
        ];
    }
}
