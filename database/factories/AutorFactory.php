<?php

namespace Database\Factories;

use App\Models\Autor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Autor>
 */
class AutorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->firstName(),
            'apellido' => fake()->lastName(),
            'fecha_nacimiento' => fake()->dateTimeBetween('-90 years', '-25 years')->format('Y-m-d'),
            'nacionalidad' => fake()->country(),
            'biografia' => fake()->paragraph(),
        ];
    }
}
