<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'to' => fake()->word(),
            'message' => fake()->word(),
            'status' => fake()->word(),
        ];
    }
}
