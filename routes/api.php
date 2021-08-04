<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TagController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);

// Route::get('',[StoryController::class, 'get']);
Route::get('/story/getAllStories',[StoryController::class, 'getAllStories']);
Route::get('/story/getLatestStories',[StoryController::class, 'getLatestStories']);
Route::get('/story/getSimilarStories',[StoryController::class, 'getSimilarStories']);
Route::get('/story/getStoriesByCategory',[StoryController::class, 'getStoriesByCategory']);

Route::post('/story/increaseViewCount',[StoryController::class, 'increaseViewCount']);

Route::post('/comment/getComments', [CommentController::class, 'getComments']);

Route::get('/category', [CategoryController::class, 'getCategories']);

Route::middleware('auth:sanctum')->group(function() {

    Route::prefix('category')->group(function () {
        Route::post('/', [CategoryController::class, 'createCategory']);
        Route::get('/{id}', [CategoryController::class, 'getCategory']);
        Route::put('/update/{id}', [CategoryController::class, 'updateCategory']);
        Route::delete('/delete/{id}', [CategoryController::class, 'deleteCategory']);
    });

    Route::prefix('comment')->group(function () {
        Route::get('/', [CommentController::class, 'getComments']);
        Route::post('/', [CommentController::class, 'createComment']);
        Route::get('/{id}', [CommentController::class, 'getComment']);
        Route::put('/update/{id}', [CommentController::class, 'updateComment']);
        Route::delete('/delete/{id}', [CommentController::class, 'deleteComment']);
    });

    Route::prefix('story')->group(function () {
        Route::get('/', [StoryController::class, 'getStories']);
        Route::post('/', [StoryController::class, 'createStory']);
        Route::get('/{id}', [StoryController::class, 'getStory']);
        Route::put('/update/{id}', [StoryController::class, 'updateStory']);
        Route::delete('/delete/{id}', [StoryController::class, 'deleteStory']);
    });

    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'getUsers']);
        Route::get('/{id}', [UserController::class, 'getUser']);
        Route::post('/updatePassword', [UserController::class, 'updatePassword']);
        Route::post('/updateProfilePhoto', [UserController::class, 'updateProfilePhoto']);
        Route::delete('/delete/{id}', [UserController::class, 'deleteUser']);
    });

    // Route::post('/me', [AuthController::class, 'me']);

});
