<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        $classroom = Classroom::factory()->create();

        $student = Student::factory()->create([
            'class_id' => $classroom->id,
        ]);

        return [
            'student_id' => $student->id,
            'class_id' => $classroom->id,
            'date' => now()->toDateString(),
            'status' => fake()->randomElement(['present', 'absent', 'permission']),
            'marked_by' => User::factory()->state([
                'role' => 'teacher',
            ]),
        ];
    }
}

