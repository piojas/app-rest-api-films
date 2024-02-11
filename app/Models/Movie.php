<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'country',
        'cover_image',
        'year',
        'duration',
        'director',
        'screenplay',
    ];

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'genre_movies');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
