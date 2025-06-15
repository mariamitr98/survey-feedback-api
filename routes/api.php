<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\JWTAuthController;
use App\Http\Controllers\ResponderController;
use App\Http\Controllers\SurveyController;
use App\Http\Middleware\JwtMiddleware;

Route::get('/surveys', [SurveyController::class, 'getActiveSurveys']);
Route::get('/surveys/{id}', [SurveyController::class, 'getSurveyById']);

Route::post('/register', [JWTAuthController::class, 'register']);
Route::post('/login', [JWTAuthController::class, 'login']);

// JWT Secure Endpoinst
Route::middleware([JwtMiddleware::class])->group(function () {

    Route::post('/surveys/{id}/submit', [SurveyController::class, 'submitAnswers']);
    Route::get('/me', [ResponderController::class, 'getResponderInfo']);

    Route::get('/logout', [JWTAuthController::class, 'logout']);
});
