<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateAccountRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Account Management
 *
 * API endpoints for managing user account. Authenticated users can retrieve their account details, update their account details, and change their password.
 */
class AccountController extends Controller
{
    /**
     * Get account details
     *
     * This endpoint allows an authenticated user to retrieve their account details.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->respondSuccessWithData(__('Account details retrieved successfully.'), $user);
    }

    /**
     * Update account details
     *
     * This endpoint allows an authenticated user to update their account details.
     */
    public function update(UpdateAccountRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->update($request->validated());

        return $this->respondSuccessWithData(__('Account details updated successfully.'), $user->fresh());
    }

    /**
     * Change account password
     *
     * This endpoint allows an authenticated user to change their account password.
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();
        $validatedData = $request->validated();

        if ($validatedData['current_password'] === $validatedData['password']) {
            return $this->respondFailedValidation(__('The new password must be different from the current password.'));
        }

        $user->update(['password' => $validatedData['password']]);

        // Revoke all tokens except the current one
        $user->tokens()->where('id', '!=', $request->user()->token()->id)->delete();

        return $this->respondSuccess(__('Password changed successfully.'));
    }
}
