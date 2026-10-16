<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('tickets.index') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100">
                            #TK-{{ str_pad((string)$ticket->id, 5, '0', STR_PAD_LEFT) }}
                        </span>
                        @if($ticket->status === 'completed')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Selesai (Completed)
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                Menunggu Tindak Lanjut (Pending)
                            </span>
                        @endif
                    </div>
                    <h2 class="font-bold text-xl sm:text-2xl text-slate-800 leading-tight mt-1">
                        {{ $ticket->subject }}
                    </h2>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('tickets.create') }}" 
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Buat Tiket Lain</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Card Rincian Aduan -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8 space-y-6">
                    
                    <!-- Metadata Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                        <div>
                            <span class="text-slate-400 block font-medium">Tanggal Terjadi Insiden:</span>
                            <span class="font-semibold text-slate-800 text-sm mt-0.5 block">
                                {{ $ticket->incident_date ? $ticket->incident_date->format('l, d F Y') : '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Waktu Pengajuan:</span>
                            <span class="font-semibold text-slate-800 text-sm mt-0.5 block">
                                {{ $ticket->created_at->format('d M Y, H:i') }} WIB
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Kegiatan Webinar Terkait:</span>
                            @if($ticket->event)
                                <a href="{{ route('events.room', $ticket->event->id) }}" class="font-semibold text-indigo-600 hover:text-indigo-800 text-sm mt-0.5 block truncate" title="{{ $ticket->event->title }}">
                                    {{ $ticket->event->title }} ↗
                                </a>
                            @else
                                <span class="font-semibold text-slate-500 text-sm mt-0.5 block">Kendala Sistem Umum</span>
                            @endif
                        </div>
                    </div>

                    <!-- Keterangan Laporan Kendala -->
                    <div>
                        <h4 class="font-bold text-slate-800 text-sm uppercase tracking-wider mb-2">Deskripsi & Keterangan Pengaduan</h4>
                        <div class="p-4 rounded-xl bg-slate-50/50 border border-slate-200/60 text-slate-700 text-sm leading-relaxed whitespace-pre-line font-normal">
                            {{ $ticket->description }}
                        </div>
                    </div>

                </div>
            </div>

            <!-- Card Tanggapan / Resolusi dari Administrator BKN -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8">
                    <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                        Tanggapan & Penyelesaian Helpdesk BKN
                    </h3>

                    @if($ticket->status === 'completed')
                        <div class="p-5 rounded-xl bg-emerald-50/70 border border-emerald-200 space-y-3">
                            <div class="flex items-center justify-between text-xs text-emerald-800 border-b border-emerald-200/60 pb-3">
                                <span class="font-semibold flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Diselesaikan oleh: <span class="font-bold">{{ $ticket->resolver ? $ticket->resolver->name : 'Administrator BKN' }}</span>
                                </span>
                                <span class="text-emerald-700">
                                    {{ $ticket->resolved_at ? $ticket->resolved_at->format('d M Y, H:i') : '-' }} WIB
                                </span>
                            </div>
                            <div class="text-sm text-slate-800 leading-relaxed whitespace-pre-line pt-1">
                                {{ $ticket->admin_notes }}
                            </div>
                        </div>
                    @else
                        <!-- Pending state waiting for admin response -->
                        <div class="p-5 rounded-xl bg-amber-50 border border-amber-200 text-center sm:text-left sm:flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 mx-auto sm:mx-0">
                                <svg class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </div>
                            <div class="mt-3 sm:mt-0">
                                <h4 class="font-bold text-amber-900 text-sm">Sedang Dalam Antrean Peninjauan Admin</h4>
                                <p class="text-xs text-amber-800 mt-1">
                                    Laporan Anda telah berhasil dicatat dengan status <span class="font-bold">Pending</span>. Tim helpdesk BKN akan segera menganalisis log sistem dan merespons keluhan Anda di halaman ini.
                                </p>
                            </div>
                        </div>
                    @endif

                    @if(Auth::user()->hasRole('admin'))
                        <!-- Quick Jump for Admin -->
                        <div class="mt-6 pt-6 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-xs text-slate-500">Anda login sebagai Administrator.</span>
                            <a href="{{ route('admin.tickets.show', $ticket) }}" 
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                                <span>Buka Panel Manajemen Admin & Balas Tiket</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
