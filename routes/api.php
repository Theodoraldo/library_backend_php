<?php

use App\Http\Controllers\Api\V1\AttendanceController;
use App\Http\Controllers\Api\V1\AuthorController;
use App\Http\Controllers\Api\V1\BookController;
use App\Http\Controllers\Api\V1\BorrowHistoryController;
use App\Http\Controllers\Api\V1\GenreController;
use App\Http\Controllers\Api\V1\LibraryPatronController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckAdminRole;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// User signup, login and logout
Route::controller(UserController::class)->group(function () {
    Route::post('/signup', 'signup');
    Route::post('/signin', 'signin');
    Route::post('/signout', 'signout')->middleware('auth:sanctum');
});

Route::group(['prefix' => 'api/v1', 'middleware' => ['auth:sanctum']], function () {
    Route::apiResource('genre', GenreController::class)->except(['destroy']);
    Route::apiResource('genre', GenreController::class)->middleware(CheckAdminRole::class)->only(['destroy']);
    Route::apiResource('author', AuthorController::class)->except(['destroy']);
    Route::apiResource('author', AuthorController::class)->middleware(CheckAdminRole::class)->only(['destroy']);
    Route::apiResource('book', BookController::class)->except(['destroy']);
    Route::apiResource('book', BookController::class)->middleware(CheckAdminRole::class)->only(['destroy']);
    Route::apiResource('patron', LibraryPatronController::class)->except(['destroy']);
    Route::apiResource('patron', LibraryPatronController::class)->middleware(CheckAdminRole::class)->only(['destroy']);
    Route::apiResource('borrower', BorrowHistoryController::class);
    Route::apiResource('attendance', AttendanceController::class);

    // Created purposely for reports
    Route::get('most-book', [BorrowHistoryController::class, 'getLatestBookBorrowed']);
});
