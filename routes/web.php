<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EnqueteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AdminRegistrationController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [EnqueteController::class, 'index'])->name('dashboard');
    Route::resource('enquetes', EnqueteController::class)->except(['index']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['super_admin'])->group(function () {
        Route::get('/approvals', [AdminController::class, 'showApprovals'])->name('approvals');
        Route::post('/approve/{user}', [AdminController::class, 'approve'])->name('approve');
        Route::post('/reject/{user}', [AdminController::class, 'reject'])->name('reject');
    });
});

Route::middleware(['auth'])->prefix('guest')->name('guest.')->group(function () {
    Route::get('/dashboard', [GuestController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/enquete/{enquete}', [EnqueteController::class, 'showPublic'])->name('enquetes.showPublic');
    Route::post('/enquete/{enquete}/votar', [EnqueteController::class, 'votar'])->name('enquetes.votar');
    Route::get('/enquete/{enquete}/resultados', [EnqueteController::class, 'getResultadosJson'])->name('enquetes.resultados');
});

require __DIR__.'/auth.php';

Route::get('/admin/register', [AdminRegistrationController::class, 'create'])->name('admin.register.form');
Route::post('/admin/register', [AdminRegistrationController::class, 'store'])->name('admin.register.store');
