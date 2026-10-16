<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Pusat Bantuan & Pengaduan Kendala
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Ajukan laporan permasalahan teknis, sertifikat, atau absensi kegiatan BKN RI dan pantau status tindak lanjutnya.
                </p>
            </div>
            <a href="{{ route('tickets.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 shadow-sm shadow-indigo-200 transition-all duration-200 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Buat Tiket Aduan Baru</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Summary KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Aduan Anda</div>
                        <div class="text-2xl font-bold text-slate-800 mt-0.5">{{ $tickets->total() }}</div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Dalam Proses (Pending)</div>
                        <div class="text-2xl font-bold text-amber-600 mt-0.5">{{ $pendingCount }}</div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Selesai (Completed)</div>
                        <div class="text-2xl font-bold text-emerald-600 mt-0.5">{{ $completedCount }}</div>
                    </div>
                </div>
            </div>

            <!-- Ticket List Table / Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg">Riwayat Tiket & Pengaduan</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar laporan yang pernah Anda sampaikan ke tim helpdesk BKN.</p>
                    </div>
                </div>

                @if($tickets->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-semibold uppercase tracking-wider">
                                    <th class="py-3.5 px-6">ID Tiket</th>
                                    <th class="py-3.5 px-6">Subjek Permasalahan</th>
                                    <th class="py-3.5 px-6">Kegiatan Terkait</th>
                                    <th class="py-3.5 px-6">Tanggal Insiden</th>
                                    <th class="py-3.5 px-6">Status</th>
                                    <th class="py-3.5 px-6 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($tickets as $ticket)
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="py-4 px-6 font-mono font-bold text-xs text-indigo-600">
                                            #TK-{{ str_pad((string)$ticket->id, 5, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td class="py-4 px-6">
                                            <a href="{{ route('tickets.show', $ticket) }}" class="font-semibold text-slate-800 hover:text-indigo-600 transition-colors line-clamp-1">
                                                {{ $ticket->subject }}
                                            </a>
                                            <span class="text-xs text-slate-400 block mt-0.5">
                                                Diajukan {{ $ticket->created_at->diffForHumans() }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-xs text-slate-600">
                                            @if($ticket->event)
                                                <span class="font-medium text-slate-700 block max-w-xs truncate" title="{{ $ticket->event->title }}">
                                                    {{ $ticket->event->title }}
                                                </span>
                                            @else
                                                <span class="text-slate-400 italic">Kendala Umum / Sistem</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 text-xs text-slate-600 font-medium">
                                            {{ $ticket->incident_date ? $ticket->incident_date->format('d M Y') : '-' }}
                                        </td>
                                        <td class="py-4 px-6">
                                            @if($ticket->status === 'completed')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Completed
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                    Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <a href="{{ route('tickets.show', $ticket) }}" 
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition-colors">
                                                <span>Detail</span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($tickets->hasPages())
                        <div class="p-6 border-t border-slate-100">
                            {{ $tickets->links() }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <h4 class="font-bold text-slate-800 text-lg">Belum Ada Tiket Aduan</h4>
                        <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">
                            Anda belum pernah mengajukan tiket pengaduan atau kendala. Jika mengalami kendala saat presensi atau sertifikat, silakan buat tiket baru.
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('tickets.create') }}" 
                               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm shadow-indigo-200 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>Buat Tiket Pertama</span>
                            </a>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
