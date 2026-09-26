<?php

declare(strict_types=1);

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterEventController;
use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {

    // Fitur Open Account & Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Register Event
    Route::post('/events/register', RegisterEventController::class)->name('events.register');

    // Admin and Dosen Routes
    Route::middleware('verified')->group(function () {
        
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');

        // Subgroup for Admin
        Route::middleware([EnsureUserHasRole::class . ':admin'])
            ->prefix('admin')
            ->name('admin.')
            ->group(function () {
                Route::get('/events/create', function () {
                    return "Admin Create Event Page";
                })->name('events.create');
            });

        // Subgroup for Dosen
        Route::middleware([EnsureUserHasRole::class . ':dosen'])
            ->prefix('dosen')
            ->name('dosen.')
            ->group(function () {
                Route::get('/my-events', function () {
                    return "Dosen my Event Page";
                })->name('events.index');
            });
    });
});

require __DIR__.'/auth.php';
