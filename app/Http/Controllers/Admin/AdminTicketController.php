<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminTicketController extends Controller
{
    /**
     * Menampilkan daftar tiket aduan pengguna untuk admin helpdesk BKN.
     */
    public function index(Request $request): View
    {
        $query = Ticket::query()
            ->with(['user:id,name,email', 'event:id,title']);

        // Filter status: all, pending, completed
        $filter = (string) $request->input('status', 'all');
        if ($filter === 'pending') {
            $query->pending();
        } elseif ($filter === 'completed') {
            $query->completed();
        }

        // Pencarian subjek, keterangan, atau nama peserta
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $tickets = $query->latest()
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'total' => Ticket::count(),
            'pending' => Ticket::pending()->count(),
            'completed' => Ticket::completed()->count(),
        ];

        return view('admin.tickets.index', compact('tickets', 'stats'));
    }

    /**
     * Menampilkan rincian tiket aduan untuk ditindaklanjuti admin.
     */
    public function show(Ticket $ticket): View
    {
        $ticket->load(['user', 'event:id,title', 'resolver:id,name']);

        return view('admin.tickets.show', compact('ticket'));
    }

    /**
     * Memperbarui status tiket (pending -> completed) dan memberikan catatan solusi.
     */
    public function update(Ticket $ticket, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,completed'],
            'admin_notes' => ['required', 'string', 'max:2000'],
        ]);

        $status = $validated['status'];
        $isCompleting = $status === 'completed';

        $ticket->update([
            'status' => $status,
            'admin_notes' => $validated['admin_notes'],
            'resolved_at' => $isCompleting ? Carbon::now() : null,
            'resolved_by' => $isCompleting ? Auth::id() : null,
        ]);

        $statusLabel = $isCompleting ? 'Selesai (Completed)' : 'Pending';

        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', "Status tiket berhasil diubah menjadi {$statusLabel} dan tanggapan telah disimpan.");
    }
}
