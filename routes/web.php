<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClassController;
use Illuminate\Support\Facades\Route;

// Define routes for authentication
Route::get('/', [AuthController::class, 'login'])->name('login'); // Route for displaying login form
Route::post('login', [AuthController::class, 'AuthLogin']); // Route for handling login form submission
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('forgot-password', [AuthController::class, 'forgotpassword'])->name('forgot-password');
Route::post('forgot-password', [AuthController::class, 'PostForgotPassword'])->name('forgot-password');
Route::get('reset/{token}', [AuthController::class, 'reset'])->name('reset');
Route::post('reset/{token}', [AuthController::class, 'PostReset'])->name('PostReset');

// Define routes for dashboards with middleware
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('admin/dashboard', [DashboardController::class, 'dashboard']);
    Route::get('admin/admin/list', [AdminController::class, 'list']);
    Route::get('admin/admin/add', [AdminController::class, 'add']);
    Route::post('admin/admin/add', [AdminController::class, 'insert']);
    Route::get('admin/admin/edit/{id}', [AdminController::class, 'edit']);
    Route::post('admin/admin/edit/{id}', [AdminController::class, 'update']);
    Route::post('admin/admin/delete/{id}', [AdminController::class, 'deleteUser'])->name('admin.deleteUser');
    Route::get('admin/deleted-admins', [AdminController::class, 'showDeletedAdmins'])->name('admin.showDeletedAdmins');
    Route::post('admin/recover-admin/{id}', [AdminController::class, 'recoverAdmin'])->name('admin.recoverAdmin');

    //class url
    Route::get('admin/class/list', [ClassController::class, 'list']);
    Route::get('admin/class/add', [ClassController::class, 'add']);
    Route::post('admin/class/add', [ClassController::class, 'insert']);
    Route::get('admin/class/edit/{id}', [ClassController::class, 'edit']);
    Route::post('admin/class/edit/{id}', [ClassController::class, 'update']);
    Route::post('admin/class/delete/{id}', [ClassController::class, 'delete'])->name('admin.class.delete');
    Route::match(['get', 'post'], 'admin/class/archived', [ClassController::class, 'viewArchived'])->name('admin.class.archived');
    Route::post('admin/class/restore/{id}', [ClassController::class, 'restore']);

});

Route::middleware(['auth', 'teacher'])->group(function () {
    Route::get('teacher/dashboard', [DashboardController::class, 'dashboard']);
});

Route::middleware(['auth', 'student'])->group(function () {
    Route::get('student/dashboard', [DashboardController::class, 'dashboard']);
});
Route::middleware(['auth', 'parent'])->group(function () {
    Route::get('parent/dashboard', [DashboardController::class, 'dashboard']);
});
