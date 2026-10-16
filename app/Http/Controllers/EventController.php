<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Throwable;

class EventController extends Controller
{
    /**
     * Menampilkan daftar acara di Dasbor.
     * Menerapkan Eager Loading untuk mencegah N+1 Query Problem.
     */
    public function index(Request $request): View
    {
        try {
            $user = $request->user();

            // Memuat status registrasi dan presensi pengguna aktif (O(1) in-memory Hash Map)
            // Mengatasi N+1 Problem: Tepat 1 kueri terindeks dimuat ke memori
            $userRegistrations = $user
                ? $user->registeredEvents()
                    ->select('events.id')
                    ->withPivot(['is_attended', 'attended_at'])
                    ->get()
                    ->keyBy('id')
                : collect();

            // Hash lookup array untuk kompatibilitas pengecekan pendaftaran O(1)
            $userRegisteredEventIds = $userRegistrations->keys()->flip()->all();

            $query = Event::query()
                ->with(['speakers' => function ($subQuery) {
                    $subQuery->select('users.id', 'users.name');
                }])
                ->withCount('registeredUsers');

            // Fitur Pencarian Judul, Deskripsi, atau Pemateri
            if ($request->filled('search')) {
                $search = trim((string) $request->input('search'));
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('speakers', function ($sq) use ($search) {
                          $sq->where('name', 'like', "%{$search}%");
                      });
                });
            }

            // Filter status: all, upcoming, past, registered
            $filter = (string) $request->input('filter', 'all');
            if ($filter === 'upcoming') {
                $query->where('end_time', '>=', now());
            } elseif ($filter === 'past') {
                $query->where('end_time', '<', now());
            } elseif ($filter === 'registered' && $user) {
                $registeredIds = $userRegistrations->keys()->all();
                $query->whereIn('id', $registeredIds);
            }

            // Urutan kegiatan (kegiatan yang akan datang diutamakan)
            $events = $query->orderBy('start_time', 'asc')
                ->paginate(9)
                ->withQueryString();

            // Statistik ringkas untuk UI Hero banner
            $stats = [
                'total' => Event::count(),
                'upcoming' => Event::where('end_time', '>=', now())->count(),
                'registered' => $userRegistrations->count(),
                'attended' => $userRegistrations->where('pivot.is_attended', true)->count(),
            ];

            return view('dashboard', compact('events', 'userRegistrations', 'userRegisteredEventIds', 'stats'));

        } catch (Throwable $e) {
            Log::error('Gagal memuat daftar acara: ' . $e->getMessage());

            $emptyPaginator = new LengthAwarePaginator(collect(), 0, 9, 1, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);

            $stats = ['total' => 0, 'upcoming' => 0, 'registered' => 0, 'attended' => 0];

            return view('dashboard', [
                'events' => $emptyPaginator,
                'userRegistrations' => collect(),
                'userRegisteredEventIds' => [],
                'stats' => $stats,
            ]);
        }
    }
}
