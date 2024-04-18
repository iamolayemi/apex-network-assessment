<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;

it('retrieves a list of users successfully', function () {
    Passport::actingAs(User::factory()->create());

    User::factory()->count(10)->create();

    $this->getJson(route('users.index'))
        ->assertStatus(200)
        ->assertJsonCount(10, 'data');
});

it('retrieves the details of a user successfully', function () {
    Passport::actingAs($user = User::factory()->create());

    $this->getJson(route('users.show', $user))
        ->assertStatus(200)
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.name', $user->name)
        ->assertJsonPath('data.email', $user->email);
});

it('creates a new user successfully when authenticated as an admin', function () {
    Passport::actingAs(User::factory()->create(['role' => UserRole::ADMIN]));

    $payload = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => UserRole::USER,
    ];

    $this->postJson(route('users.store'), $payload)
        ->assertSuccessful()
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonPath('data.email', $payload['email']);

    $this->assertDatabaseCount(User::class, 2); // Admin user is created by default
    $this->assertDatabaseHas(User::class, [
        'name' => $payload['name'],
        'email' => $payload['email'],
    ]);
});

it('fails to create a new user when authenticated as a user', function () {
    Passport::actingAs(User::factory()->create(['role' => UserRole::USER]));

    $payload = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => UserRole::USER,
    ];

    $this->postJson(route('users.store'), $payload)->assertForbidden();

    $this->assertDatabaseCount(User::class, 1);
    $this->assertDatabaseMissing(User::class, [
        'name' => $payload['name'],
        'email' => $payload['email'],
    ]);
});

it('fails to create a new user with invalid data', function () {
    Passport::actingAs(User::factory()->create(['role' => UserRole::ADMIN]));

    $payload = ['name' => 'Test User'];

    $this->postJson(route('users.store'), $payload)->assertUnprocessable();

    $this->assertDatabaseCount(User::class, 1);
    $this->assertDatabaseMissing(User::class, ['name' => $payload['name']]);
});

it('updates a user details successfully', function () {
    Passport::actingAs(User::factory()->create(['role' => UserRole::ADMIN]));
    $user = User::factory()->create();

    $payload = [
        'name' => fake()->name(),
        'email' => fake()->unique()->safeEmail(),
        'role' => UserRole::USER->value,
    ];

    $this->putJson(route('users.update', $user), $payload)->assertSuccessful();

    expect($user->fresh()->name)->toBe($payload['name'])
        ->and($user->fresh()->email)->toBe($payload['email']);
});

it('fails to update a user with invalid data', function () {
    Passport::actingAs(User::factory()->create(['role' => UserRole::ADMIN]));
    $user = User::factory()->create();

    $payload = [
        'name' => fake()->name(),
        'email' => fake()->unique()->safeEmail(),
        'role' => 'invalid-role',
    ];

    $this->putJson(route('users.update', $user), $payload)->assertUnprocessable();

    expect($user->fresh()->name)->not()->toBe($payload['name'])
        ->and($user->fresh()->email)->not()->toBe($payload['email']);
});

it('fails to update a user when authenticated as a user', function () {
    Passport::actingAs(User::factory()->create(['role' => UserRole::USER]));
    $user = User::factory()->create();

    $payload = [
        'name' => fake()->name(),
        'email' => fake()->unique()->safeEmail(),
        'role' => UserRole::USER->value,
    ];

    $this->putJson(route('users.update', $user), $payload)->assertForbidden();

    expect($user->fresh()->name)->not()->toBe($payload['name'])
        ->and($user->fresh()->email)->not()->toBe($payload['email']);
});

it('deletes a user successfully', function () {
    Passport::actingAs(User::factory()->create(['role' => UserRole::ADMIN]));
    $user = User::factory()->create();

    $this->deleteJson(route('users.destroy', $user))->assertSuccessful();

    $this->assertDatabaseCount(User::class, 1);
    $this->assertDatabaseMissing(User::class, ['id' => $user->id]);
});

it('fails to delete a user when authenticated as a user', function () {
    Passport::actingAs(User::factory()->create(['role' => UserRole::USER]));
    $user = User::factory()->create();

    $this->deleteJson(route('users.destroy', $user))->assertForbidden();

    $this->assertDatabaseCount(User::class, 2);
    $this->assertDatabaseHas(User::class, ['id' => $user->id]);
});

it('resets a user password successfully', function () {
    Passport::actingAs(User::factory()->create(['role' => UserRole::ADMIN]));
    $user = User::factory()->create();

    $response = $this->postJson(route('users.reset-password', $user))->assertSuccessful();

    $this->assertTrue(Hash::check($response->json('data.password'), $user->fresh()->password));
});

it('fails to reset a user password when authenticated as a user', function () {
    Passport::actingAs(User::factory()->create(['role' => UserRole::USER]));
    $user = User::factory()->create();

    $this->postJson(route('users.reset-password', $user))->assertForbidden();
});
