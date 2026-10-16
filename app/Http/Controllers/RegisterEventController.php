<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Exception;
use Throwable;

class RegisterEventController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        // 1. Sanitasi dan Validasi Input Dasar
        // Mencegah Mass Assignment & SQL Injection dari Endpoint API
        $validated = $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
        ]);

        $user = $request->user();
        $eventId = (int) $validated['event_id'];

        try {
            // 2. Memulai Database Transaction dengan batas toleransi Deadlock (5 kali coba ulang)
            // Memenuhi prinsip Atomicity & Isolation pada ACID
            DB::transaction(function () use ($user, $eventId) {
                
                // 3. Pessimistic Locking: Mengunci baris acara spesifik
                // Proses lain yang ingin membaca/menulis event ini harus mengantre O(1)
                $event = Event::lockForUpdate()->findOrFail($eventId);

                if ($event->is_finished) {throw new Exception('EVENT_FINISHED');
}
                // 4. Hitung pendaftar saat ini (berbasis row-lock yang aman)
                $currentRegistrants = $event->registeredUsers()->count();
                
                if ($currentRegistrants >= $event->capacity) {
                    // Melempar exception agar transaksi (DB::rollBack) dibatalkan secara otomatis
                    throw new Exception('QUOTA_FULL');
                }

                // 5. Operasi Penulisan (Attach) ke Pivot Table
                $user->registeredEvents()->attach($eventId);
                
            }, 5);

            // Jika sampai di baris ini, transaksi commit sukses
            // (Catatan: GenerateCertificateJob dipindah ke modul Presensi Hibrida)
            return redirect()->back()->with('success', 'Berhasil mendaftar untuk acara ini!');

        } catch (QueryException $e) {
            // 6. Error Handling: Pendaftaran Ganda (Unique Constraint MySQL 1062)
            // Mengandalkan proteksi level basis data jauh lebih cepat daripada memanggil kueri exist()
            if ($e->errorInfo[1] === 1062) {
                return redirect()->back()->with('warning', 'Anda sudah terdaftar di acara ini.');
            }
            
            // Log dicatat tanpa mengekspos ID pengguna ke publik (Keamanan PII)
            Log::error('DB Error saat registrasi event:', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan pada pangkalan data.');

        } catch (Exception $e) {
            // Menangkap galat logika kustom (Kuota penuh)
            if ($e->getMessage() === 'QUOTA_FULL') {
                return redirect()->back()->with('error', 'Mohon maaf, kuota acara ini sudah penuh.');
            }

            if ($e->getMessage() === 'EVENT_FINISHED') {
                return redirect()->back()->with('error', 'Pendaftaran ditutup. Acara telah dilaksanakan.');
            }
            Log::error('Logika Error saat registrasi:', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem.');

        } catch (Throwable $e) {
            // Menangkap galat fatal lainnya (Graceful Degradation)
            Log::critical('Fatal Error Registrasi:', [
                'user_id' => $user->id,
                'event_id' => $eventId,
                'message' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Gagal memproses pendaftaran. Silakan coba lagi.');
        }
    }
}