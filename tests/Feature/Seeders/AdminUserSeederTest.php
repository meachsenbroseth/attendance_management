<?php

use App\Models\User;
use Database\Seeders\AdminUserSeeder;

test('admin seeder creates an admin user', function () {
    $this->seed(AdminUserSeeder::class);

    $admin = User::query()->where('email', 'admin@example.com')->first();

    expect($admin)->not->toBeNull()
        ->and($admin->role)->toBe('admin');
});
