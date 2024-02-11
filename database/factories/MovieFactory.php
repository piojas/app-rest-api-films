<?php

namespace Database\Factories;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movie>
 */
class MovieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $genres = Genre::pluck('id')->toArray(); 

        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'country' => $this->faker->country(),
            'year' => $this->faker->year(),
            'duration' => $this->faker->numberBetween(60, 240), 
            'director' => $this->faker->name(),
            'screenplay' => $this->faker->name(),
        ];
    }
}
