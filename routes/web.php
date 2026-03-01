<?php

use App\Http\Controllers\MemoryController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [MemoryController::class, 'index'])->name('memories.index');
Route::get('/post', [MemoryController::class, 'create'])->name('memories.create');
Route::post('/post', [MemoryController::class, 'store'])->name('memories.store');
Route::post('/report/{memory}', [MemoryController::class, 'report'])->name('memories.report');

// Admin routes (basic auth protected)
Route::middleware('auth.basic')->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/approve/{memory}', [AdminController::class, 'approve'])->name('admin.approve');
    Route::post('/reject/{memory}', [AdminController::class, 'reject'])->name('admin.reject');
});
