<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\TagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::group([
    "middleware" => "auth:sanctum"
], 
function (){
    Route::get('userprofile', [AuthController::class, 'userprofile']);
    Route::post('/logout', [AuthController::class, 'logout']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tags_view', [TagController::class, 'view']);
    Route::post('/tags_create', [TagController::class, 'create']);
    Route::put('/tags_update/{id}', [TagController::class, 'update']);
    Route::delete('/tags_destroy/{id}', [TagController::class, 'delete']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/posts_view', [PostController::class, 'view']);  // View all posts
    Route::get('/posts_single/{id}', [PostController::class, 'show']);  // View a single post
    Route::post('/posts_create', [PostController::class, 'create']);  // Create a new post
    Route::put('/posts_update/{id}', [PostController::class, 'update']);  // Update a post
    Route::delete('/posts_delete/{id}', [PostController::class, 'delete']);  // Soft delete a post
    Route::get('/posts/trashed', [PostController::class, 'trashed']);  // View deleted posts
    Route::post('/posts/restore/{id}', [PostController::class, 'restore']);  // Restore a deleted post
});

Route::get('/stats', [StatsController::class, 'stats']);