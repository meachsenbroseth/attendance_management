<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('admins can view the users index page', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    User::factory()->create([
        'role' => 'teacher',
    ]);

    $this
        ->actingAs($admin)
        ->get(route('users.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('users/index')
            ->has('users.data'),
        );
});

test('non-admins cannot view the users index page', function () {
    $teacher = User::factory()->create([
        'role' => 'teacher',
    ]);

    $this
        ->actingAs($teacher)
        ->get(route('users.index'))
        ->assertForbidden();
});

test('admins can view the users create page', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $this
        ->actingAs($admin)
        ->get(route('users.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('users/create'),
        );
});

test('non-admins cannot view the users create page', function () {
    $teacher = User::factory()->create([
        'role' => 'teacher',
    ]);

    $this
        ->actingAs($teacher)
        ->get(route('users.create'))
        ->assertForbidden();
});

test('admins can create users with a role', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $response = $this
        ->actingAs($admin)
        ->post(route('users.store'), [
            'name' => 'Teacher One',
            'email' => 'teacher1@example.com',
            'password' => 'password123',
            'role' => 'teacher',
        ]);

    $response->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', [
        'email' => 'teacher1@example.com',
        'role' => 'teacher',
    ]);
});

test('non-admins cannot create users', function () {
    $teacher = User::factory()->create([
        'role' => 'teacher',
    ]);

    $response = $this
        ->actingAs($teacher)
        ->post(route('users.store'), [
            'name' => 'Admin Two',
            'email' => 'admin2@example.com',
            'password' => 'password123',
            'role' => 'admin',
        ]);

    $response->assertForbidden();
});

test('role must be one of the allowed values', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $response = $this
        ->actingAs($admin)
        ->post(route('users.store'), [
            'name' => 'Bad Role',
            'email' => 'badrole@example.com',
            'password' => 'password123',
            'role' => 'superuser',
        ]);

    $response->assertSessionHasErrors('role');
});
