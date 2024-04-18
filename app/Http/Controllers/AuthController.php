<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Knuckles\Scribe\Attributes\Group;

/**
 * @group Authentication
 *
 * API endpoints for authentication. This includes logging in, registering, and logging out.
 */
class AuthController extends Controller
{
    /**
     * Login to an account
     *
     * Log in to an account using email and password. Returns the user info and an access token.
     *
     * @unauthenticated
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $user = User::where('email', $validatedData['email'])->firstOrFail();

        if (! Hash::check($validatedData['password'], $user->password)) {
            return $this->respondFailedValidation(__('The provided credentials does not match our records.'));
        }

        $personalAccessToken = $user->createToken('accessToken');

        return $this->respondSuccessWithData(__('Your account has been logged in successfully.'), [
            'user' => $user,
            'accessToken' => $personalAccessToken->accessToken,
            'accessTokenExpiration' => $personalAccessToken->token->expires_at->getTimestamp(),
        ]);
    }

    /**
     * Register a new account
     *
     * Register a new user account. Returns the user info and an access token.
     *
     * @unauthenticated
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create(array_merge(
            $request->safe()->except('password_confirmation'), [
                'email_verified_at' => now(),
                'role' => UserRole::USER,
            ])
        );

        $personalAccessToken = $user->createToken('accessToken');

        return $this->respondSuccessWithData(__('Your account has been created successfully.'), [
            'user' => $user,
            'accessToken' => $personalAccessToken->accessToken,
            'accessTokenExpiration' => $personalAccessToken->token->expires_at->getTimestamp(),
        ]);
    }

    /**
     * Logout from an account
     *
     * Log out from an account. Revokes the current access token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->delete();

        return $this->respondSuccess(__('Your account has been logged out successfully.'));
    }
}
