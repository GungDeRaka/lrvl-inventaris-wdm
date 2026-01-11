<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RuanganFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_ruangan' => $this->faker->unique()->word . ' Room',
        ];
    }
}