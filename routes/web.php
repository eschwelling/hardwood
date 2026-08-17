<?php

use App\Http\Controllers\MemoryController;
use App\Http\Controllers\MixtapeController;
use App\Http\Controllers\DunkController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [MemoryController::class, 'index'])->name('memories.index');
Route::get('/post', [MemoryController::class, 'create'])->name('memories.create');
Route::post('/post', [MemoryController::class, 'store'])->name('memories.store');
Route::post('/report/{memory}', [MemoryController::class, 'report'])->name('memories.report');
Route::post('/resonate/{memory}', [MemoryController::class, 'resonate'])->name('memories.resonate');
Route::post('/annotate/{memory}', [MemoryController::class, 'annotate'])->name('memories.annotate');

Route::post('/mixtapes', [MixtapeController::class, 'store'])->name('mixtapes.store');
Route::get('/mixtapes/{mixtape}', [MixtapeController::class, 'show'])->name('mixtapes.show');

Route::get('/dunks', [DunkController::class, 'index'])->name('dunks.index');
Route::post('/dunks', [DunkController::class, 'store'])->name('dunks.store')->middleware('throttle:10,1');

Route::get('/admin/login', [AdminController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login')->middleware('throttle:5,1');
Route::post('/admin/exit', [AdminController::class, 'exit'])->name('admin.exit');

// Admin routes (session auth, via the login form above)
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/approve/{memory}', [AdminController::class, 'approve'])->name('admin.approve');
    Route::post('/reject/{memory}', [AdminController::class, 'reject'])->name('admin.reject');
    Route::post('/delete/{memory}', [AdminController::class, 'destroy'])->name('admin.destroy');
    Route::post('/annotations/{annotation}/approve', [AdminController::class, 'approveAnnotation'])->name('admin.annotations.approve');
    Route::post('/annotations/{annotation}/reject', [AdminController::class, 'rejectAnnotation'])->name('admin.annotations.reject');
    Route::post('/annotations/{annotation}/delete', [AdminController::class, 'destroyAnnotation'])->name('admin.annotations.destroy');
    Route::post('/mixtapes/{mixtape}/approve', [AdminController::class, 'approveMixtape'])->name('admin.mixtapes.approve');
    Route::post('/mixtapes/{mixtape}/reject', [AdminController::class, 'rejectMixtape'])->name('admin.mixtapes.reject');
    Route::post('/dunks/{dunk}/approve', [AdminController::class, 'approveDunk'])->name('admin.dunks.approve');
    Route::post('/dunks/{dunk}/reject', [AdminController::class, 'rejectDunk'])->name('admin.dunks.reject');
    Route::post('/dunks/{dunk}/delete', [AdminController::class, 'destroyDunk'])->name('admin.dunks.destroy');
});
