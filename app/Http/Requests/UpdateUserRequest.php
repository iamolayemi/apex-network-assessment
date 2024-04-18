<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->route('user')?->id)],
            'role' => ['required', 'string', Rule::enum(UserRole::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => __('The provided email is already in use by another user.'),
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
            'role' => [
                'example' => 'user',
                'enum' => UserRole::class,
            ],
        ];
    }
}
