<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @group User Management
 *
 * APIs for managing users. Only admins can create, update, reset password, and delete users.
 */
class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('role:admin', except: ['index', 'show']),
        ];
    }

    /**
     * Get all users
     *
     * This endpoint allows you to get all users.
     *
     * @queryParam page The current page number. Example: 1
     * @queryParam per_page The number of items to display per page. Example: 10
     */
    public function index(Request $request)
    {
        $users = User::paginate(perPage: $request->get('per_page', 10), page: $request->get('page', 1));

        return $this->respondSuccessWithData(__('Users retrieved successfully.'), $users);
    }

    /**
     * Get a specific user
     *
     * This endpoint allows you to get the details of a specific user.
     */
    public function show(User $user)
    {
        return $this->respondSuccessWithData(__('User retrieved successfully.'), $user);
    }

    /**
     * Create a user
     *
     * This endpoint allows an admin to create a new user.
     */
    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->safe()->except(['password_confirmation']));

        return $this->respondSuccessWithData(__('User created successfully.'), $user);
    }

    /**
     * Update a user
     *
     * This endpoint allows an admin to update the details of a specific user.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        if ($request->user()->id === $user->id) {
            return $this->respondForbidden(__('You cannot update your own account.'));
        }

        $user->update($request->validated());

        return $this->respondSuccess(__('User updated successfully.'));
    }

    /**
     * Delete a user
     *
     * This endpoint allows an admin to delete a specific user.
     */
    public function destroy(Request $request, User $user)
    {
        if ($request->user()->id === $user->id) {
            return $this->respondForbidden(__('You cannot delete your own account.'));
        }

        $user->delete();

        return $this->respondSuccess(__('User deleted successfully.'));
    }

    /**
     * Reset user password
     *
     * This endpoint allows an admin to reset the password of a specific user.
     */
    public function resetPassword(Request $request, User $user)
    {
        if ($request->user()->id === $user->id) {
            return $this->respondForbidden(__('You cannot reset your own password.'));
        }

        $password = Str::random(16);

        $user->update(['password' => Hash::make($password)]);

        return $this->respondSuccessWithData(__('Password reset successfully.'), ['password' => $password]);
    }
}
