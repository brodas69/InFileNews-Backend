<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\AuthController;
use Laravel\Socialite\Facades\Socialite;

Route::post('/auth/register',    [AuthController::class,'register']);
Route::post('/auth/login',    [AuthController::class,'login']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgot']);
Route::post('/auth/reset-password', [AuthController::class, 'reset_password']);


// Route::get('/auth/redirect', [AuthController::class, 'redirect']);
// Route::get('/auth/callback', [AuthController::class, 'callback']);

Route::middleware('auth:api')->group(function () {
    Route::post('/auth/logout',  [AuthController::class,'logout']);
    Route::apiResource('category', CategoryController::class);
    Route::apiResource('news', NewsController::class);

});


