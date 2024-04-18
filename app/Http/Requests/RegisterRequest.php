<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'string', Password::default()],
            'password_confirmation' => ['required', 'string', 'same:password'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => __('The provided email is already registered by another user.'),
            'password_confirmation.same' => __('The password confirmation does not match.'),
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    public function bodyParameters(): array
    {
        return [
            'name' => [
                'example' => 'John Doe',
            ],
            'email' => [
                'example' => 'user@testing.com',
            ],
            'password' => [
                'example' => 'Password123!',
            ],
            'password_confirmation' => [
                'example' => 'Password123!',
            ],
        ];
    }
}
