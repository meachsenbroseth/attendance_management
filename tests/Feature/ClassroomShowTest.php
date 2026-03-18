<?php

use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('authenticated users can view a classroom with students', function () {
    $user = User::factory()->create([
        'role' => 'teacher',
    ]);

    $classroom = Classroom::factory()->create();

    Student::factory()->create([
        'class_id' => $classroom->id,
    ]);

    $this
        ->actingAs($user)
        ->get(route('classrooms.show', $classroom))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('classrooms/show')
            ->where('classroom.id', $classroom->id)
            ->has('classroom.students', 1),
        );
});
