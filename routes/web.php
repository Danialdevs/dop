<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AcademicYearSelectionController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('groups.index'));

Route::resource('groups', GroupController::class);
Route::get('groups/{group}/lessons/create', [LessonController::class, 'create'])->name('groups.lessons.create');
Route::post('groups/{group}/lessons', [LessonController::class, 'store'])->name('groups.lessons.store');
Route::get('groups/{group}/lessons/{lesson}/edit', [LessonController::class, 'edit'])->name('groups.lessons.edit');
Route::put('groups/{group}/lessons/{lesson}', [LessonController::class, 'update'])->name('groups.lessons.update');
Route::delete('groups/{group}/lessons/{lesson}', [LessonController::class, 'destroy'])->name('groups.lessons.destroy');
Route::post('groups/{group}/lessons/copy-week', [LessonController::class, 'copyWeek'])->name('groups.lessons.copy-week');
Route::delete('groups/{group}/lessons/clear-week', [LessonController::class, 'clearWeek'])->name('groups.lessons.clear-week');

Route::get('students/{student}/password/edit', [StudentController::class, 'editPassword'])->name('students.password.edit');
Route::put('students/{student}/password', [StudentController::class, 'updatePassword'])->name('students.password.update');
Route::resource('students', StudentController::class);

Route::get('teachers/{teacher}/password/edit', [TeacherController::class, 'editPassword'])->name('teachers.password.edit');
Route::put('teachers/{teacher}/password', [TeacherController::class, 'updatePassword'])->name('teachers.password.update');
Route::resource('teachers', TeacherController::class);


Route::resource('courses', CourseController::class);
Route::get('journal', [JournalController::class, 'index'])->name('journal.index');
Route::patch('lessons/{lesson}/status', [LessonController::class, 'updateStatus'])->name('lessons.status.update');
Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
Route::post('academic-years/select', [AcademicYearSelectionController::class, 'update'])->name('academic-years.select');
