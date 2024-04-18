<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'string', Password::default()],
            'password_confirmation' => ['required', 'string', 'same:password'],
            'role' => ['required', 'string', Rule::enum(UserRole::class)],
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
                'example' => 'Test User',
            ],
            'email' => [
                'example' => 'user@example.com',
            ],
            'password' => [
                'example' => 'Password123!',
            ],
            'password_confirmation' => [
                'example' => 'Password123!',
            ],
            'role' => [
                'example' => 'user',
                'enum' => UserRole::class,
            ],
        ];
    }
}
