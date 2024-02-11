<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'country' => $this->country,
            'cover_image' => $this->cover_image,
            'year' => $this->year,
            'duration' => $this->duration,
            'director' => $this->director,
            'screenplay' => $this->screenplay,
            'genres' => $this->genres,
            'rating' => $this->ratings,
        ];
    }
}
