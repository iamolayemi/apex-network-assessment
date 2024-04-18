<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;

it('retrieves the logged in user\'s account details successfully', function () {
    Passport::actingAs($user = User::factory()->create());

    $this->getJson(route('account.show'))
        ->assertSuccessful()
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.name', $user->name);
});

it('fails to update the user account details with invalid data', function () {
    Passport::actingAs($user = User::factory()->create());

    $payload = [
        'name' => fake()->name(),
    ];

    $this->patchJson(route('account.update'), $payload)->assertUnprocessable();

    expect($user->fresh()->name)->not->toBe($payload['name']);
});

it('updates the user account details successfully with valid data', function () {
    Passport::actingAs($user = User::factory()->create());

    $file = UploadedFile::fake()->image('avatar.jpg');
    $payload = [
        'name' => fake()->name(),
        'email' => fake()->unique()->safeEmail(),
    ];

    $this->patchJson(route('account.update'), $payload)->assertSuccessful();

    expect($user->fresh()->name)->toBe($payload['name'])
        ->and($user->fresh()->email)->toBe($payload['email']);
});

it('fails to change the user account password when the current password is the same as the new password', function () {
    Passport::actingAs(User::factory()->create());

    $this->postJson(route('account.change-password'), [
        'current_password' => 'Password123!',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ])->assertUnprocessable();
});

it('changes the user account password successfully when provided with a valid new password', function () {
    Passport::actingAs($user = User::factory()->create());

    $this->postJson(route('account.change-password'), [
        'current_password' => 'Password123!',
        'password' => 'NewPassword123!',
        'password_confirmation' => 'NewPassword123!',
    ])->assertSuccessful();

    $this->assertTrue(Hash::check('NewPassword123!', $user->fresh()->password));
});
