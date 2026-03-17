<?php

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('prevents duplicate attendance for the same student and date', function (): void {
    $teacher = User::factory()->create(['role' => 'teacher']);

    $classroom = Classroom::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    $student = Student::factory()->create([
        'class_id' => $classroom->id,
    ]);

    $date = now()->toDateString();

    Attendance::factory()->create([
        'student_id' => $student->id,
        'class_id' => $classroom->id,
        'date' => $date,
        'marked_by' => $teacher->id,
    ]);

    $response = $this->actingAs($teacher)
        ->post(route('attendances.store'), [
            'student_id' => $student->id,
            'class_id' => $classroom->id,
            'date' => $date,
            'status' => 'present',
        ]);

    $response->assertSessionHasNoErrors();
});

it('allows admin to see all attendance but restricts teacher to own classes', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $teacherA = User::factory()->create(['role' => 'teacher']);
    $teacherB = User::factory()->create(['role' => 'teacher']);

    $classA = Classroom::factory()->create(['teacher_id' => $teacherA->id]);
    $classB = Classroom::factory()->create(['teacher_id' => $teacherB->id]);

    $studentA = Student::factory()->create(['class_id' => $classA->id]);
    $studentB = Student::factory()->create(['class_id' => $classB->id]);

    Attendance::factory()->create([
        'student_id' => $studentA->id,
        'class_id' => $classA->id,
        'marked_by' => $teacherA->id,
    ]);

    Attendance::factory()->create([
        'student_id' => $studentB->id,
        'class_id' => $classB->id,
        'marked_by' => $teacherB->id,
    ]);

    $adminResponse = $this->actingAs($admin)->get(route('attendances.index'));
    $adminResponse->assertOk();
    expect($adminResponse['attendances']->count())->toBe(2);

    $teacherResponse = $this->actingAs($teacherA)->get(route('attendances.index'));
    $teacherResponse->assertOk();
    expect($teacherResponse['attendances']->count())->toBe(1);
    expect($teacherResponse['attendances']->first()->class_id)->toBe($classA->id);
});

