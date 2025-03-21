<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::post('/tasks', [TaskController::class, 'store']);
Route::get('/tasks', [TaskController::class, 'index']);
Route::put('/tasks/{id}', [TaskController::class, 'update']);
Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
Route::post('/tasks/{id}/dependencies', [TaskController::class, 'addDependency']);
Route::delete('/tasks/{id}/dependencies', [TaskController::class, 'removeDependency']);
Route::get('/tasks/{id}/dependencies', [TaskController::class, 'getDependencies']);
Route::get('/tasks/{id}/can-start', [TaskController::class, 'canStart']);
