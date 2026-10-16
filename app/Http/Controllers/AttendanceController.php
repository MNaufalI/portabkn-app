<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use App\Jobs\GenerateCertificateJob;
use App\Services\PdfGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;
use Throwable;

class AttendanceController extends Controller
{
    /**
     * Memproses absensi kehadiran peserta dengan Live Token dua sesi (Sesi Awal & Sesi Akhir).
     * Menerapkan perhitungan selisih waktu (time-delta) O(1) berbasis threshold durasi kegiatan.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Sanitasi dan Validasi Input
        $validated = $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'live_token' => ['required', 'string', 'max:50'],
            'session_type' => ['nullable', 'string', 'in:start,end,auto'],
        ]);

        $user = $request->user();
        $eventId = (int) $validated['event_id'];
        $inputToken = trim($validated['live_token']);
        $requestedSession = $validated['session_type'] ?? 'auto';

        try {
            // 2. O(1) Primary Key Lookup entitas acara
            $event = Event::findOrFail($eventId);

            // 3. Verifikasi apakah pengguna sudah terdaftar sebagai peserta
            $registration = $user->registeredEvents()
                ->where('events.id', $eventId)
                ->first();

            if (! $registration) {
                return redirect()->back()->with('error', 'Presensi gagal: Anda belum terdaftar sebagai peserta pada kegiatan ini.');
            }

            // 4. Deteksi Sesi Presensi (Sesi Awal vs Sesi Akhir)
            $isStartTokenMatch = $event->start_token && hash_equals((string) $event->start_token, $inputToken);
            $isEndTokenMatch = $event->end_token && hash_equals((string) $event->end_token, $inputToken);
            $isLegacyTokenMatch = $event->live_token && hash_equals((string) $event->live_token, $inputToken);

            // Tentukan sesi yang akan diproses
            $targetSession = null;
            if ($requestedSession === 'start' || ($requestedSession === 'auto' && $isStartTokenMatch)) {
                $targetSession = 'start';
            } elseif ($requestedSession === 'end' || ($requestedSession === 'auto' && $isEndTokenMatch)) {
                $targetSession = 'end';
            } elseif ($isLegacyTokenMatch) {
                // Mode fallback kompatibilitas single token
                $targetSession = 'legacy';
            }

            if (! $targetSession) {
                return redirect()->back()->with('error', 'Token presensi tidak valid. Pastikan token yang Anda masukkan sesuai dengan sesi yang diumumkan narasumber.');
            }

            // ==========================================
            // KASUS A: PRESENSI SESI AWAL (CHECK-IN)
            // ==========================================
            if ($targetSession === 'start') {
                if (! $isStartTokenMatch && ! $isLegacyTokenMatch) {
                    return redirect()->back()->with('error', 'Token sesi awal tidak cocok. Silakan periksa kembali.');
                }

                // Cek apakah sudah pernah check-in
                if ($registration->pivot->checked_in_at) {
                    $checkedInTime = Carbon::parse($registration->pivot->checked_in_at)->format('H:i');
                    return redirect()->back()->with('warning', "Anda sudah melakukan presensi sesi awal pada pukul {$checkedInTime} WIB.");
                }

                // Validasi batas toleransi keterlambatan 15 menit sejak acara dimulai
                $startTime = $event->start_time;
                if ($startTime) {
                    $toleranceDeadline = Carbon::parse($startTime)->addMinutes(15);
                    if (Carbon::now()->gt($toleranceDeadline)) {
                        return redirect()->back()->with('error', 'Presensi sesi awal ditolak: Batas toleransi keterlambatan (15 menit sejak acara dimulai) telah berakhir.');
                    }
                }

                // Catat waktu check-in (ACID Transaction)
                DB::transaction(function () use ($user, $eventId) {
                    $user->registeredEvents()->updateExistingPivot($eventId, [
                        'checked_in_at' => Carbon::now(),
                    ]);
                });

                return redirect()->back()->with('success', 'Presensi sesi awal berhasil dicatat! Silakan ikuti materi hingga selesai dan masukkan token sesi akhir untuk mendapatkan sertifikat.');
            }

            // ==========================================
            // KASUS B: PRESENSI SESI AKHIR (CHECK-OUT & VALIDASI THRESHOLD)
            // ==========================================
            if ($targetSession === 'end') {
                if (! $isEndTokenMatch) {
                    return redirect()->back()->with('error', 'Token sesi akhir tidak cocok. Silakan periksa kembali.');
                }

                // Cek prasyarat: harus sudah presensi sesi awal
                if (! $registration->pivot->checked_in_at) {
                    return redirect()->back()->with('error', 'Presensi sesi akhir ditolak: Anda belum mencatat presensi sesi awal saat kegiatan dimulai.');
                }

                // Cek jika sudah pernah diverifikasi selesai
                if ($registration->pivot->is_attended) {
                    $attendedTime = $registration->pivot->attended_at 
                        ? Carbon::parse($registration->pivot->attended_at)->format('d M Y, H:i') 
                        : 'sebelumnya';

                    return redirect()->back()->with('warning', "Kehadiran Anda sudah terverifikasi lengkap pada {$attendedTime} WIB.");
                }

                $checkedInAt = Carbon::parse($registration->pivot->checked_in_at);
                $now = Carbon::now();

                // Hitung selisih waktu kehadiran aktual (Time-Delta)
                $durationMinutes = (int) $checkedInAt->diffInMinutes($now);
                $thresholdMinutes = (int) ($event->attendance_threshold_minutes ?? 30);

                // Validasi ambang batas (Threshold)
                if ($thresholdMinutes > 0 && $durationMinutes < $thresholdMinutes) {
                    return redirect()->back()->with('error', "Durasi kehadiran Anda baru {$durationMinutes} menit (syarat minimal: {$thresholdMinutes} menit). Silakan ikuti pemaparan materi terlebih dahulu.");
                }

                // Simpan status kehadiran penuh & stempel waktu akhir (ACID Transaction)
                DB::transaction(function () use ($user, $eventId, $now) {
                    $user->registeredEvents()->updateExistingPivot($eventId, [
                        'checked_out_at' => $now,
                        'is_attended' => true,
                        'attended_at' => $now,
                    ]);
                });

                // Dispatch Asynchronous Background Job untuk Pembuatan Sertifikat PDF
                try {
                    GenerateCertificateJob::dispatch($user, $event);
                } catch (Throwable $jobException) {
                    Log::error('Gagal memasukkan GenerateCertificateJob ke dalam antrean: ' . $jobException->getMessage());
                }

                return redirect()->back()->with('success', "Presensi sesi akhir berhasil! Anda telah mengikuti kegiatan selama {$durationMinutes} menit (memenuhi syarat minimal {$thresholdMinutes} menit). Sertifikat Anda telah diproses!");
            }

            // ==========================================
            // KASUS C: LEGACY SINGLE-TOKEN MODE
            // ==========================================
            if ($registration->pivot->is_attended) {
                return redirect()->back()->with('warning', "Anda sudah tercatat hadir pada kegiatan ini.");
            }

            DB::transaction(function () use ($user, $eventId) {
                $now = Carbon::now();
                $user->registeredEvents()->updateExistingPivot($eventId, [
                    'checked_in_at' => $registration->pivot->checked_in_at ?? $now,
                    'checked_out_at' => $now,
                    'is_attended' => true,
                    'attended_at' => $now,
                ]);
            });

            try {
                GenerateCertificateJob::dispatch($user, $event);
            } catch (Throwable $jobException) {
                Log::error('Gagal memasukkan GenerateCertificateJob ke dalam antrean: ' . $jobException->getMessage());
            }

            return redirect()->back()->with('success', 'Presensi berhasil diverifikasi! Kehadiran Anda telah dicatat dan sertifikat kelulusan sedang diproses.');

        } catch (Throwable $e) {
            Log::critical('Fatal Error pada Modul Presensi:', [
                'user_id' => $user->id,
                'event_id' => $eventId,
                'message' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memproses presensi. Silakan coba lagi.');
        }
    }

    /**
     * Mengunduh sertifikat kehadiran PDF bagi peserta yang telah terverifikasi hadir.
     */
    public function downloadCertificate(Event $event, Request $request, PdfGeneratorService $pdfService): StreamedResponse
    {
        $user = $request->user();

        // Verifikasi apakah user terdaftar dan telah hadir
        $registration = $user->registeredEvents()
            ->where('events.id', $event->id)
            ->first();

        if (! $registration || ! $registration->pivot->is_attended) {
            abort(403, 'Akses Ditolak: Anda belum menyelesaikan presensi kehadiran untuk kegiatan ini.');
        }

        $fileName = "certificates/{$event->id}_{$user->id}.pdf";

        // Jika file belum di-generate di antrean, generate secara on-demand
        if (! Storage::disk('public')->exists($fileName)) {
            $pdfService->generate($user, $event);
        }

        return Storage::disk('public')->download(
            $fileName, 
            'Sertifikat_BKN_' . str_replace(' ', '_', $event->title) . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }
}
