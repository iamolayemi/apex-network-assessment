<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ChangePasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', Password::default()],
            'password_confirmation' => ['required', 'string', 'same:password'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.current_password' => __('The current password is incorrect.'),
            'password_confirmation.same' => __('The password confirmation does not match.'),
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    public function bodyParameters(): array
    {
        return [
            'current_password' => [
                'example' => 'Password123!',
            ],
            'password' => [
                'example' => 'NewPassword123!',
            ],
            'password_confirmation' => [
                'example' => 'NewPassword123!',
            ],
        ];
    }
}
