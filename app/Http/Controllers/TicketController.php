<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TicketController extends Controller
{
    /**
     * Menampilkan daftar tiket aduan / keluhan milik pengguna yang sedang login.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $tickets = $user->tickets()
            ->with(['event:id,title'])
            ->latest()
            ->paginate(10);

        $pendingCount = $user->tickets()->pending()->count();
        $completedCount = $user->tickets()->completed()->count();

        return view('tickets.index', compact('tickets', 'pendingCount', 'completedCount'));
    }

    /**
     * Menampilkan formulir pembuatan tiket aduan baru.
     */
    public function create(Request $request): View
    {
        $user = $request->user();

        // Ambil daftar event yang diikuti user untuk pilihan dropdown
        $userEvents = $user->registeredEvents()
            ->select('events.id', 'events.title')
            ->get();

        $selectedEventId = $request->query('event_id') ? (int) $request->query('event_id') : null;

        return view('tickets.create', compact('userEvents', 'selectedEventId'));
    }

    /**
     * Menyimpan aduan / kendala baru dari peserta.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:150'],
            'event_id' => ['nullable', 'integer', 'exists:events,id'],
            'incident_date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:2000'],
        ]);

        $ticket = $request->user()->tickets()->create([
            'subject' => $validated['subject'],
            'event_id' => $validated['event_id'] ?? null,
            'incident_date' => $validated['incident_date'],
            'description' => $validated['description'],
            'status' => 'pending',
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Tiket keluhan berhasil diajukan! Tim Administrator BKN akan segera menindaklanjuti kendala Anda.');
    }

    /**
     * Menampilkan rincian tiket aduan dan tanggapan dari admin.
     */
    public function show(Ticket $ticket, Request $request): View
    {
        $user = $request->user();

        // Otorisasi: hanya pemilik tiket atau admin yang berhak melihat
        if ($ticket->user_id !== $user->id && ! $user->hasRole('admin')) {
            abort(403, 'Akses Ditolak: Anda bukan pemilik tiket ini.');
        }

        $ticket->load(['event:id,title', 'resolver:id,name']);

        return view('tickets.show', compact('ticket'));
    }
}
