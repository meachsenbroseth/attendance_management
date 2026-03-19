<?php

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('upserts attendance (one record per student per day)', function (): void {
    $teacher = User::factory()->create(['role' => 'teacher']);

    $classroom = Classroom::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    $student = Student::factory()->create([
        'class_id' => $classroom->id,
    ]);

    $date = now()->toDateString();

    $this->actingAs($teacher)
        ->post(route('attendances.store', $classroom), [
            'date' => $date,
            'attendances' => [
                [
                    'student_id' => $student->id,
                    'status' => 'present',
                ],
            ],
        ])
        ->assertSessionHasNoErrors();

    $this->actingAs($teacher)
        ->post(route('attendances.store', $classroom), [
            'date' => $date,
            'attendances' => [
                [
                    'student_id' => $student->id,
                    'status' => 'permission',
                ],
            ],
        ])
        ->assertSessionHasNoErrors();

    expect(Attendance::query()->where('student_id', $student->id)->where('date', $date)->count())->toBe(1);
    expect(Attendance::query()->where('student_id', $student->id)->where('date', $date)->value('status'))->toBe('permission');
});

it('rejects late status and accepts permission', function (): void {
    $teacher = User::factory()->create(['role' => 'teacher']);

    $classroom = Classroom::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    $student = Student::factory()->create([
        'class_id' => $classroom->id,
    ]);

    $date = now()->toDateString();

    $this->actingAs($teacher)
        ->post(route('attendances.store', $classroom), [
            'date' => $date,
            'attendances' => [
                [
                    'student_id' => $student->id,
                    'status' => 'late',
                ],
            ],
        ])
        ->assertSessionHasErrors('attendances.0.status');

    $this->actingAs($teacher)
        ->post(route('attendances.store', $classroom), [
            'date' => $date,
            'attendances' => [
                [
                    'student_id' => $student->id,
                    'status' => 'permission',
                ],
            ],
        ])
        ->assertSessionHasNoErrors();
});

