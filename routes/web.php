<?php

declare(strict_types=1);

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterEventController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventRoomController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\Admin\AdminEventController;
use App\Http\Controllers\Admin\AdminTicketController;
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

    // Presensi Hibrida dengan Live Token & Unduh Sertifikat
    Route::post('/events/presence', [AttendanceController::class, 'store'])->name('events.presence');
    Route::get('/events/{event}/certificate', [AttendanceController::class, 'downloadCertificate'])->name('events.certificate');

    // Ruang Kegiatan / Live Webinar & Unduh Materi Paparan
    Route::get('/events/{event}/room', [EventRoomController::class, 'show'])->name('events.room');
    Route::get('/events/{event}/material', [EventRoomController::class, 'downloadMaterial'])->name('events.material');

    // Pusat Bantuan & Pengaduan Kendala Peserta (Helpdesk Tickets)
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');

    // Admin and Dosen Routes
    Route::middleware('verified')->group(function () {
        
        Route::get('/dashboard', [EventController::class, 'index'])->name('dashboard');

        // Subgroup Khusus Administrator BKN
        Route::middleware([EnsureUserHasRole::class . ':admin'])
            ->prefix('admin')
            ->name('admin.')
            ->group(function () {
                // Manajemen Acara (CRUD & State)
                Route::get('/events', [AdminEventController::class, 'index'])->name('events.index');
                Route::get('/events/create', [AdminEventController::class, 'create'])->name('events.create');
                Route::post('/events', [AdminEventController::class, 'store'])->name('events.store');
                Route::get('/events/{event}/edit', [AdminEventController::class, 'edit'])->name('events.edit');
                Route::put('/events/{event}', [AdminEventController::class, 'update'])->name('events.update');
                Route::delete('/events/{event}', [AdminEventController::class, 'destroy'])->name('events.destroy');
                Route::get('/events/{event}/export', [AdminEventController::class, 'exportAttendees'])->name('events.export');

                // Manajemen Tiket Kendala Peserta
                Route::get('/tickets', [AdminTicketController::class, 'index'])->name('tickets.index');
                Route::get('/tickets/{ticket}', [AdminTicketController::class, 'show'])->name('tickets.show');
                Route::patch('/tickets/{ticket}', [AdminTicketController::class, 'update'])->name('tickets.update');
            });

        // Subgroup for Dosen / Narasumber
        Route::middleware([EnsureUserHasRole::class . ':dosen'])
            ->prefix('dosen')
            ->name('dosen.')
            ->group(function () {
                Route::get('/my-events', function () {
                    return redirect()->route('dashboard', ['filter' => 'upcoming']);
                })->name('events.index');
            });
    });
});

require __DIR__.'/auth.php';
