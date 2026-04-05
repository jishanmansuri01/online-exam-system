<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ExamController as AdminExamController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Admin\ResultController as AdminResultController;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Student\ExamController as StudentExamController;
use App\Http\Controllers\Student\ResultController as StudentResultController;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes(['register' => true]);

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Exam management
    Route::resource('exams', AdminExamController::class);

    // Question management
    Route::get('exams/{exam}/questions', [AdminQuestionController::class, 'index'])->name('questions.index');
    Route::get('exams/{exam}/questions/create', [AdminQuestionController::class, 'create'])->name('questions.create');
    Route::post('exams/{exam}/questions', [AdminQuestionController::class, 'store'])->name('questions.store');
    Route::get('questions/{question}/edit', [AdminQuestionController::class, 'edit'])->name('questions.edit');
    Route::put('questions/{question}', [AdminQuestionController::class, 'update'])->name('questions.update');
    Route::delete('questions/{question}', [AdminQuestionController::class, 'destroy'])->name('questions.destroy');

    // Results
    Route::get('/results', [AdminResultController::class, 'index'])->name('results.index');
    Route::get('/results/{result}/download', [AdminResultController::class, 'download'])->name('results.download');
});

// Student routes
Route::prefix('student')->name('student.')->middleware(['auth', 'role:student'])->group(function () {

    Route::get('/dashboard', [StudentDashboard::class, 'index'])->name('dashboard');

    // Exam
    Route::get('/exams', [StudentExamController::class, 'index'])->name('exams.index');
    Route::get('/exams/{exam}/start', [StudentExamController::class, 'start'])->name('exams.start');
    Route::get('/exams/{exam}/take', [StudentExamController::class, 'take'])->name('exams.take');
    Route::post('/exams/{exam}/submit', [StudentExamController::class, 'submit'])->name('exams.submit');

    // Results
    Route::get('/results', [StudentResultController::class, 'index'])->name('results.index');
    Route::get('/results/{result}/download', [StudentResultController::class, 'download'])->name('results.download');
});
