<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\JWTAuthController;
use App\Http\Controllers\ResponderController;
use App\Http\Controllers\SurveyController;
use App\Http\Middleware\JwtMiddleware;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/surveys', [SurveyController::class, 'getActiveSurveys']);

Route::get('/surveys/{id}', [SurveyController::class, 'getSurveyById']);

//Register
Route::post('/register', [JWTAuthController::class, 'register']);

//Login
Route::post('/login', [JWTAuthController::class, 'login']);

// JWT Secure Endpoinst
Route::middleware([JwtMiddleware::class])->group(function () {
    
    Route::post('/surveys/{id}/submit', function (int $user_id) {
        return 'submit' . $user_id;
    });

    Route::get('/me',[ResponderController::class, 'getResponderInfo']);

    //Logout
    Route::get('/logout', [JWTAuthController::class, 'logout']);

});
