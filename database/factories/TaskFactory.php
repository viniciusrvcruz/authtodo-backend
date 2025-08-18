<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'description' => fake()->text(500),
            'is_completed' => fake()->boolean(),
            'user_id' => User::factory(),
        ];
    }
}
