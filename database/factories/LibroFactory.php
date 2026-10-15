<?php

namespace Database\Factories;

use App\Models\Libro;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Libro>
 */
class LibroFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titulo' => rtrim(fake()->sentence(3), '.'),
            'isbn' => fake()->unique()->isbn13(),
            'anio_publicacion' => fake()->numberBetween(1950, 2024),
            'numero_paginas' => fake()->numberBetween(80, 900),
            'descripcion' => fake()->paragraph(),
            'stock_disponible' => fake()->numberBetween(1, 8),
        ];
    }
}
