<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;

Route::get('/students', [StudentController::class, 'index']);
Route::post('/students', [StudentController::class, 'store']);
Route::get('/students/{id}', [StudentController::class, 'show']);
Route::patch('/students/{id}', [StudentController::class, 'update']);

Route::get('/students/{id}/subjects', [StudentController::class, 'getSubjects']);

Route::get('/students/{id}/subjects', [SubjectController::class, 'index']);
Route::post('/students/{id}/subjects', [SubjectController::class, 'store']);
Route::get('/students/{id}/subjects/{subject_id}', [SubjectController::class, 'show']);
Route::patch('/students/{id}/subjects/{subject_id}', [SubjectController::class, 'update']);
