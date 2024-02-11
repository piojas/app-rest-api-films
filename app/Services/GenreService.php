<?php

namespace App\Services;

use App\Models\Genre;

class GenreService
{
    public function isGenresExist(array $genreIds): bool
    {
        $existingGenresCount = Genre::whereIn('id', $genreIds)->count();

        return $existingGenresCount === count($genreIds);
    }
}
