<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminEventController extends Controller
{
    /**
     * Menampilkan daftar seluruh acara untuk manajemen admin BKN.
     */
    public function index(Request $request): View
    {
        $query = Event::query()
            ->with(['speakers:id,name'])
            ->withCount('registeredUsers');

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $events = $query->orderBy('start_time', 'desc')
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'total' => Event::count(),
            'upcoming' => Event::where('end_time', '>=', now())->count(),
            'finished' => Event::where('end_time', '<', now())->count(),
        ];

        return view('admin.events.index', compact('events', 'stats'));
    }

    /**
     * Menampilkan formulir pembuatan acara baru.
     */
    public function create(): View
    {
        $dosenRole = Role::where('name', 'dosen')->first();
        $potentialSpeakers = User::when($dosenRole, fn($q) => $q->where('role_id', $dosenRole->id))
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        // Rekomendasi token acak 8 karakter
        $defaultStartToken = strtoupper(Str::random(8));
        $defaultEndToken = strtoupper(Str::random(8));

        return view('admin.events.create', compact('potentialSpeakers', 'defaultStartToken', 'defaultEndToken'));
    }

    /**
     * Menyimpan acara baru ke basis data.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'capacity' => ['required', 'integer', 'min:1'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'start_token' => ['required', 'string', 'max:50'],
            'end_token' => ['required', 'string', 'max:50'],
            'attendance_threshold_minutes' => ['required', 'integer', 'min:0'],
            'youtube_url' => ['nullable', 'string', 'max:255'],
            'material_file' => ['nullable', 'file', 'mimes:pdf,ppt,pptx,zip,rar', 'max:20480'],
            'speaker_ids' => ['nullable', 'array'],
            'speaker_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $materialPath = null;
        if ($request->hasFile('material_file')) {
            $materialPath = $request->file('material_file')->store('materials', 'public');
        }

        $event = DB::transaction(function () use ($validated, $materialPath) {
            $newEvent = Event::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'capacity' => (int) $validated['capacity'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'live_token' => $validated['start_token'], // kompatibilitas
                'start_token' => $validated['start_token'],
                'end_token' => $validated['end_token'],
                'attendance_threshold_minutes' => (int) $validated['attendance_threshold_minutes'],
                'youtube_url' => $validated['youtube_url'] ?? null,
                'material_path' => $materialPath,
            ]);

            if (! empty($validated['speaker_ids'])) {
                $newEvent->speakers()->sync($validated['speaker_ids']);
            }

            return $newEvent;
        });

        return redirect()->route('admin.events.index')
            ->with('success', "Kegiatan '{$event->title}' berhasil dibuat dan siap dipublikasikan!");
    }

    /**
     * Menampilkan formulir edit acara.
     */
    public function edit(Event $event): View
    {
        $dosenRole = Role::where('name', 'dosen')->first();
        $potentialSpeakers = User::when($dosenRole, fn($q) => $q->where('role_id', $dosenRole->id))
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        $selectedSpeakerIds = $event->speakers()->pluck('users.id')->toArray();

        return view('admin.events.edit', compact('event', 'potentialSpeakers', 'selectedSpeakerIds'));
    }

    /**
     * Memperbarui data acara di basis data.
     */
    public function update(Event $event, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'capacity' => ['required', 'integer', 'min:1'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'start_token' => ['required', 'string', 'max:50'],
            'end_token' => ['required', 'string', 'max:50'],
            'attendance_threshold_minutes' => ['required', 'integer', 'min:0'],
            'youtube_url' => ['nullable', 'string', 'max:255'],
            'material_file' => ['nullable', 'file', 'mimes:pdf,ppt,pptx,zip,rar', 'max:20480'],
            'speaker_ids' => ['nullable', 'array'],
            'speaker_ids.*' => ['integer', 'exists:users,id'],
        ]);

        if ($request->hasFile('material_file')) {
            // Hapus file lama jika ada
            if ($event->material_path && Storage::disk('public')->exists($event->material_path)) {
                Storage::disk('public')->delete($event->material_path);
            }
            $event->material_path = $request->file('material_file')->store('materials', 'public');
        }

        DB::transaction(function () use ($event, $validated) {
            $event->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'capacity' => (int) $validated['capacity'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'start_token' => $validated['start_token'],
                'end_token' => $validated['end_token'],
                'attendance_threshold_minutes' => (int) $validated['attendance_threshold_minutes'],
                'youtube_url' => $validated['youtube_url'] ?? null,
            ]);

            $event->speakers()->sync($validated['speaker_ids'] ?? []);
        });

        return redirect()->route('admin.events.index')
            ->with('success', "Kegiatan '{$event->title}' berhasil diperbarui!");
    }

    /**
     * Menghapus acara dari basis data.
     */
    public function destroy(Event $event): RedirectResponse
    {
        $title = $event->title;

        if ($event->material_path && Storage::disk('public')->exists($event->material_path)) {
            Storage::disk('public')->delete($event->material_path);
        }

        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', "Kegiatan '{$title}' berhasil dihapus dari sistem.");
    }

    /**
     * Mengekspor data peserta yang hadir ke dalam format CSV.
     * Menggunakan teknik Chunking dan StreamedResponse untuk mencegah Memory Leak (O(1) Space Complexity).
     */
    public function exportAttendees(Event $event): StreamedResponse
    {
        // Format penamaan file yang rapi
        $fileName = "Rekap_Peserta_BKN_{$event->id}_" . date('Ymd_Hi') . ".csv";

        return response()->streamDownload(function () use ($event) {
            // Membuka pipa aliran output secara langsung
            $handle = fopen('php://output', 'w');

            // Membuat baris Header CSV
            fputcsv($handle, ['ID Peserta', 'Nama Lengkap', 'Alamat Email', 'NIK / Identitas Pegawai', 'Status Hadir', 'Waktu Check-In', 'Waktu Check-Out']);

            // Mengambil relasi peserta (mengabaikan kolom yang tidak perlu agar kueri lebih cepat)
            // Memecah proses (chunk) per 500 baris agar memori RAM tetap di bawah batas (O(1))
            $event->registeredUsers()
                ->select('users.id', 'users.name', 'users.email', 'users.nik_encrypted')
                ->chunk(500, function ($users) use ($handle) {
                    foreach ($users as $user) {
                        
                        // Validasi status hadir dari tabel pivot
                        $isAttended = $user->pivot->is_attended ? 'Hadir (Terverifikasi)' : 'Tidak Hadir';
                        
                        // Menulis data pengguna ke baris CSV
                        fputcsv($handle, [
                            $user->id,
                            $user->name,
                            $user->email,
                            $user->nik, // NIK secara transparan akan terdekripsi oleh fitur Accessor dari Model User
                            $isAttended,
                            $user->pivot->checked_in_at ?? 'Belum Check-In',
                            $user->pivot->checked_out_at ?? '-',
                        ]);
                    }
                });

            // Menutup aliran setelah semua data selesai ditulis
            fclose($handle);
        }, $fileName, [
            // Memaksa browser untuk mengunduh sebagai file CSV
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}