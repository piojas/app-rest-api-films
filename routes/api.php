<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MoviesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
 
Route::middleware('auth:api')->group(function () {
    Route::get('user', [AuthController::class, 'userInfo']);
    Route::post('logout', [AuthController::class, 'logout']);
    
    Route::prefix('movies')->group(function () {
        Route::apiResource('/', MoviesController::class);
        Route::get('search/{title}', [MoviesController::class, 'searchByTitle']);
        Route::post('rating', [MoviesController::class, 'rateMovie']);
        Route::post('upload', [MoviesController::class, 'uploadCoverImage']);
    });
});
