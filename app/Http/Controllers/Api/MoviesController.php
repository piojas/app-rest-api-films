<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MovieResource;
use App\Http\Requests\StoreMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\Rating;
use App\Services\GenreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MoviesController extends Controller
{
    private const DEFAULT_LIMIT = 10;

    private $genreService;

    public function __construct(GenreService $genreService)
    {
        $this->genreService = $genreService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movies = Movie::with('genres')->paginate(self::DEFAULT_LIMIT);

        if ($movies->isEmpty()) {
            return response()->json(['message' => 'No movies found.'], 404);
        }

        return MovieResource::collection($movies, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMovieRequest $request)
    {
        $movieData = $request->validated();

        try {

            if ($request->hasFile('cover_image')) {
                $movieData['cover_image'] = $request->file('cover_image')->store('covers', 'public');
            }

            $movie = Movie::create($movieData);

            if ($request->has('genres')) {
                $genreIds = json_decode($request->input('genres'), true);

                if ($this->genreService->isGenresExist($genreIds)) {
                    $movie->genres()->sync($genreIds);
                }
            }

            if ($request->has('rating')) {
                $movie->ratings()->create([
                    'user_id' => auth()->id(),
                    'rating' => $request->input('rating')
                ]);
            }

            $movie->load('genres', 'ratings');

            return new MovieResource($movie, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create the movie.'], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
        return new MovieResource($movie, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMovieRequest $request, Movie $movie)
    {
        $movieData = $request->validated();
        
        try {
            if ($request->hasFile('cover_image')) {
                $movieData['cover_image'] = $request->file('cover_image')->store('covers', 'public');
            }

            $movie->update($movieData);

            if ($request->has('rating')) {
                $movie->ratings()->updateOrCreate(
                    ['user_id' => auth()->id()],
                    ['rating' => $request->input('rating')]
                );
            }

            if ($request->has('genres')) {
                $genreIds = json_decode($request->input('genres'), true);

                if ($this->genreService->isGenresExist($genreIds)) {
                    $movie->genres()->sync($genreIds);
                }
            }

            $movie->load('genres', 'ratings');

            return new MovieResource($movie, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update the movie.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie)
    {
        $movie->delete();

        return response()->noContent()->setStatusCode(204);
    }
}
