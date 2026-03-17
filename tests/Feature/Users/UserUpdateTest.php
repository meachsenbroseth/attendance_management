<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('user password is unchanged when password is blank', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
        'password' => Hash::make('old-password'),
        'role' => 'teacher',
    ]);

    $this
        ->actingAs($admin)
        ->put(route('users.update', $user), [
            'name' => 'New Name',
            'email' => 'new@example.com',
            'password' => '',
            'role' => 'student',
        ])
        ->assertRedirect(route('users.index'));

    $user->refresh();

    expect(Hash::check('old-password', $user->password))->toBeTrue();
    expect($user->name)->toBe('New Name');
    expect($user->email)->toBe('new@example.com');
    expect($user->role)->toBe('student');
});

test('user password can be updated', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    $this
        ->actingAs($admin)
        ->put(route('users.update', $user), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'password' => 'new-password',
            'role' => 'teacher',
        ])
        ->assertRedirect(route('users.index'));

    $user->refresh();

    expect(Hash::check('new-password', $user->password))->toBeTrue();
});
