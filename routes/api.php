<?php

use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('user/lists', [UserController::class, 'getUsers']);
Route::get('user/{id}', [UserController::class, 'getUserDetails']);
Route::post('user/add', [UserController::class, 'addUser']);
Route::put('user/{userId}', [UserController::class, 'updateUser']);
Route::delete('user/{userId}', [UserController::class, 'deleteUser']);

// Task Routes
Route::get('task/lists', [TaskController::class, 'getTasks']);
Route::get('task/{id}', [TaskController::class, 'getTaskDetails']);
Route::post('task/add', [TaskController::class, 'addTask']);
Route::put('task/{taskId}', [TaskController::class, 'updateTask']);
Route::delete('task/{taskId}', [TaskController::class, 'deleteTask']);