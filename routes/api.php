<?php

use App\Http\Controllers\TweetsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

# Users 
Route::post('/users', [UserController::class, 'createUser']);
Route::patch('/users/{id}/password', [UserController::class, 'patchUserById']); 
Route::put('/users/{id}', [UserController::class, 'updateUserById']);
Route::get('/users', [UserController::class, 'getUsers']);
Route::get('/users/{id}', [UserController::class, 'getUserById']);

# Tweets
Route::post('/tweets', [TweetsController::class,'postTweet']);
Route::delete('/tweets/{id}', [TweetsController::class, 'deleteTweetById']);
Route::get('/tweets/{id}', action: [TweetsController::class, 'getTweetById']);
Route::get('/my-tweets', action: [TweetsController::class, 'getMyTweets']);
Route::get('/tweets-interactions/{id}', action: [TweetsController::class, 'getTweetInteractions']);
  