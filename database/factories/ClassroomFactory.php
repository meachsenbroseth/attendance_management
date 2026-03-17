<?php

namespace Database\Factories;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Classroom>
 */
class ClassroomFactory extends Factory
{
    protected $model = Classroom::class;

    public function definition(): array
    {
        return [
            'name' => 'Grade ' . fake()->numberBetween(1, 12) . ' ' . fake()->randomLetter(),
            'teacher_id' => User::factory()->state([
                'role' => 'teacher',
            ]),
        ];
    }
}

