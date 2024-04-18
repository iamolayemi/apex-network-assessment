<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;

it('registers a new user successfully', function () {
    setUpPassport();

    $payload = [
        'name' => fake()->name(),
        'email' => fake()->email(),
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ];

    $response = $this->postJson(route('auth.register'), $payload)
        ->assertSuccessful()
        ->assertJsonPath('data.user.name', $payload['name'])
        ->assertJsonPath('data.user.email', $payload['email']);

    expect($response->json('data.accessToken'))->toBeString()
        ->and($response->json('data.accessTokenExpiration'))->toBeInt();

    $this->assertDatabaseCount(User::class, 1);
    $this->assertDatabaseHas(User::class, [
        'name' => $payload['name'],
        'email' => $payload['email'],
    ]);
});

it('fails to register a new user with invalid data', function () {
    $payload = [
        'name' => 'Test User',
        'email' => 'not an email',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $this->postJson(route('auth.register'), $payload)->assertUnprocessable();

    $this->assertDatabaseCount(User::class, 0);
    $this->assertDatabaseMissing(User::class, [
        'name' => $payload['name'],
        'email' => $payload['email'],
    ]);
});

it('logs in an existing user successfully', function () {
    setUpPassport();

    $user = User::factory()->create(['password' => Hash::make($password = 'password')]);

    $payload = [
        'email' => $user->email,
        'password' => $password,
    ];

    $response = $this->postJson(route('auth.login'), $payload)->assertSuccessful();

    expect($response->json('data.user.name'))->toBe($user->name)
        ->and($response->json('data.user.email'))->toBe($user->email)->and($response->json('data.accessToken'))->toBeString()
        ->and($response->json('data.accessTokenExpiration'))->toBeInt();

});

it('fails to log in with incorrect credentials', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $this->postJson(route('auth.login'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertUnprocessable();
});

it('logs out an authenticated user successfully', function () {
    Passport::actingAs(User::factory()->create());

    $this->deleteJson(route('auth.logout'))->assertSuccessful();
});

it('fails to log out an unauthenticated user', function () {
    $this->deleteJson(route('auth.logout'))->assertUnauthorized();
});
