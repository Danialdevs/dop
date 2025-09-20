<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('groups.index'));

Route::resource('groups', GroupController::class);
Route::resource('students', StudentController::class);
Route::resource('courses', CourseController::class);
