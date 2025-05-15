<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('users', UserController::class);
Route::apiResource('teachers', TeacherController::class);
Route::apiResource('students', StudentController::class);
Route::apiResource('industries', IndustryController::class);
Route::apiResource('internships', InternshipController::class);
