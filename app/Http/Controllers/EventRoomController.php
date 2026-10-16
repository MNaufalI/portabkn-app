<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class EventRoomController extends Controller
{
    /**
     * Menampilkan Ruang Webinar Live (Event Room).
     * Memuat embed YouTube player, materi unduhan, dan panel presensi dua sesi.
     */
    public function show(Event $event, Request $request): View|RedirectResponse
    {
        $user = $request->user();

        // 1. Verifikasi Keanggotaan Peserta (O(1) Indexed check)
        $registration = $user->registeredEvents()
            ->withPivot(['is_attended', 'attended_at', 'checked_in_at', 'checked_out_at'])
            ->where('events.id', $event->id)
            ->first();

        // Admin dan pemateri dapat masuk tanpa harus mendaftar
        $isOrganizer = $user->hasRole('admin') || $user->hasRole('dosen') || $event->speakers->contains('id', $user->id);

        if (! $registration && ! $isOrganizer) {
            return redirect()->route('dashboard')->with('warning', 'Silakan mendaftar terlebih dahulu untuk mengakses ruang kegiatan dan webinar ini.');
        }

        // 2. Logika Toleransi Keterlambatan 15 Menit sejak Acara Dimulai
        $now = Carbon::now();
        $startTime = $event->start_time;
        $isStarted = $startTime && $now->gte($startTime);
        $toleranceDeadline = $startTime ? $startTime->copy()->addMinutes(15) : null;
        $hasCheckedIn = ! empty($registration?->pivot->checked_in_at);
        $isLate = $isStarted && $toleranceDeadline && $now->gt($toleranceDeadline) && ! $hasCheckedIn;

        // Jika peserta biasa terlambat lebih dari 15 menit tanpa presensi awal, tolak akses masuk ruang
        if ($isLate && ! $isOrganizer) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak: Kegiatan sedang berjalan dan batas toleransi keterlambatan (15 menit) telah berakhir. Anda tidak dapat masuk ke ruang kegiatan karena tidak melakukan presensi awal tepat waktu.');
        }

        // Eager loading relasi pemateri untuk efisiensi kueri
        $event->load(['speakers' => function ($query) {
            $query->select('users.id', 'users.name', 'users.email');
        }]);

        // Tiket aduan yang telah dibuat oleh user untuk event ini
        $userTickets = $user->tickets()
            ->where('event_id', $event->id)
            ->latest()
            ->get();

        // Hitung menit durasi kehadiran jika sudah check-in
        $minutesAttended = 0;
        if ($registration && $registration->pivot->checked_in_at) {
            $checkedIn = Carbon::parse($registration->pivot->checked_in_at);
            $endTime = $registration->pivot->checked_out_at 
                ? Carbon::parse($registration->pivot->checked_out_at) 
                : Carbon::now();
            $minutesAttended = (int) $checkedIn->diffInMinutes($endTime);
        }

        return view('events.room', compact('event', 'registration', 'isOrganizer', 'userTickets', 'minutesAttended', 'isLate', 'isStarted', 'toleranceDeadline'));
    }

    /**
     * Mengunduh file presentasi / materi paparan narasumber.
     */
    public function downloadMaterial(Event $event, Request $request): StreamedResponse|RedirectResponse
    {
        $user = $request->user();
        $isOrganizer = $user->hasRole('admin') || $user->hasRole('dosen');
        $isRegistered = $user->registeredEvents()->where('events.id', $event->id)->exists();

        if (! $isRegistered && ! $isOrganizer) {
            return redirect()->back()->with('error', 'Akses ditolak: Anda harus terdaftar sebagai peserta untuk mengunduh materi.');
        }

        if (empty($event->material_path)) {
            return redirect()->back()->with('warning', 'Materi paparan belum diunggah oleh narasumber untuk kegiatan ini.');
        }

        // Jika path berupa link URL luar (Google Drive / Cloud storage)
        if (str_starts_with($event->material_path, 'http://') || str_starts_with($event->material_path, 'https://')) {
            return redirect()->away($event->material_path);
        }

        // Jika file tersimpan di storage lokal
        if (! Storage::disk('public')->exists($event->material_path)) {
            return redirect()->back()->with('error', 'File materi tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($event->material_path);
    }
}
