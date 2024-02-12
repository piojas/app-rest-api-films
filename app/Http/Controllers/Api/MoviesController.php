<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MovieResource;
use App\Http\Resources\RatingResource;
use App\Http\Requests\StoreMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use App\Models\Movie;
use App\Models\Rating;
use App\Services\GenreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

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

    /**
     * Search for movies by title
     */
    public function searchByTitle(string $title)
    {
        $movies = Movie::where('title', 'like', "%{$title}%")->paginate(self::DEFAULT_LIMIT);

        $movies->load('genres', 'ratings');

        return MovieResource::collection($movies, 200);
    }

    /**
     * Rate a movie
     */
    public function rateMovie(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'rating' => 'required|integer|min:1|max:10',
        ]);

        $rating = Rating::updateOrCreate(
            ['user_id' => auth()->id(), 'movie_id' => $request->input('movie_id')],
            ['rating' => $request->input('rating')]
        );

        if (!$rating) {
            return response()->json(['error' => 'Failed to rate the movie.'], 500);
        }

        return new RatingResource($rating, 200);
    }

    /**
     * Upload cover image
     */
    public function uploadCoverImage(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'cover_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if (!$request->hasFile('cover_image')) {
            return response()->json(['error' => 'No image file'], 400);
        }

        $filename = sprintf('/covers/cover_%s.%s', uniqid(), 
            $request->file('cover_image')->getClientOriginalExtension());

        Storage::disk('public')->put($filename, 
            Image::read($request->file('cover_image'))->scale(300, 300)->encode()
        );

        $updatedMovie = Movie::where('id', $request->input('movie_id'))
            ->update(['cover_image' => $filename]);

        if (!$updatedMovie) {
            return response()->json(['error' => 'Failed to update movie record'], 500);
        }

        $updatedMovie = Movie::with('genres', 'ratings')
            ->find($request->input('movie_id'));

        return response()->json([
            'cover_image_path' => '/storage' . $filename,
            'movie' => $updatedMovie
        ], 200);
    }

}
