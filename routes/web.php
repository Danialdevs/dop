<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('groups.index'));

Route::resource('groups', GroupController::class);
Route::post('groups/{group}/lessons', [LessonController::class, 'store'])->name('groups.lessons.store');

Route::get('students/{student}/password/edit', [StudentController::class, 'editPassword'])->name('students.password.edit');
Route::put('students/{student}/password', [StudentController::class, 'updatePassword'])->name('students.password.update');
Route::resource('students', StudentController::class);

Route::get('teachers/{teacher}/password/edit', [TeacherController::class, 'editPassword'])->name('teachers.password.edit');
Route::put('teachers/{teacher}/password', [TeacherController::class, 'updatePassword'])->name('teachers.password.update');
Route::resource('teachers', TeacherController::class);

Route::resource('courses', CourseController::class);
Route::get('journal', [JournalController::class, 'index'])->name('journal.index');
