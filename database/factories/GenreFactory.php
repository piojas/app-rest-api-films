<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Genre>
 */
class GenreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $genres = [
            'Akcja',
            'Animacja',
            'Biograficzny',
            'Dokumentalny',
            'Dramat',
            'Fantasy',
            'Historyczny',
            'Horror',
            'Komedia',
            'Kryminał',
            'Przygodowy',
            'Romantyczny',
            'Science fiction',
            'Thriller',
            'Wojenny'
        ];

        return [
            'name' => array_shift($genres),
        ];
    }
}
