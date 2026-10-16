<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                        Admin Helpdesk & Pusat Aduan
                    </span>
                </div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight mt-1">
                    Daftar Tiket Kendala Peserta
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Tindak lanjuti pengaduan kendala kehadiran, materi, siaran streaming, dan kendala sertifikat kegiatan BKN.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500">
                    Sistem Layanan Bantuan PortaBKN
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Stats Bar -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-xs text-slate-500 font-medium">Total Seluruh Tiket</div>
                        <div class="text-2xl font-extrabold text-slate-900 mt-1">{{ $stats['total'] ?? 0 }}</div>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-xs text-slate-500 font-medium">Menunggu Respon (Pending)</div>
                        <div class="text-2xl font-extrabold text-amber-600 mt-1">{{ $stats['pending'] ?? 0 }}</div>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-xs text-slate-500 font-medium">Selesai Ditangani (Completed)</div>
                        <div class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $stats['completed'] ?? 0 }}</div>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Toolbar -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row gap-3 items-center justify-between">
                
                <!-- Status Filter Pills -->
                @php
                    $currentStatus = request('status', 'all');
                @endphp
                <div class="flex items-center gap-1.5 overflow-x-auto w-full sm:w-auto">
                    <a href="{{ route('admin.tickets.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}"
                       class="px-3.5 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all {{ $currentStatus === 'all' ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua Tiket
                    </a>
                    <a href="{{ route('admin.tickets.index', array_merge(request()->except('status', 'page'), ['status' => 'pending'])) }}"
                       class="px-3.5 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all {{ $currentStatus === 'pending' ? 'bg-amber-600 text-white shadow-sm shadow-amber-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Pending ({{ $stats['pending'] ?? 0 }})
                    </a>
                    <a href="{{ route('admin.tickets.index', array_merge(request()->except('status', 'page'), ['status' => 'completed'])) }}"
                       class="px-3.5 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all {{ $currentStatus === 'completed' ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Selesai
                    </a>
                </div>

                <!-- Search Input -->
                <form action="{{ route('admin.tickets.index') }}" method="GET" class="w-full sm:w-80 flex items-center gap-2">
                    <input type="hidden" name="status" value="{{ $currentStatus }}">
                    <div class="relative w-full">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Cari pelapor, subjek, aduan..."
                               class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-indigo-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                    <button type="submit" class="px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-slate-800 hover:bg-slate-900 transition-colors shrink-0">
                        Cari
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.tickets.index', ['status' => $currentStatus]) }}" class="text-xs text-slate-500 hover:text-slate-800 underline shrink-0">
                            Reset
                        </a>
                    @endif
                </form>

            </div>

            <!-- Table of Tickets -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3.5 px-4 sm:px-6">No. Tiket</th>
                                <th class="py-3.5 px-4">Nama Pelapor</th>
                                <th class="py-3.5 px-4">Kegiatan Terkait</th>
                                <th class="py-3.5 px-4">Subjek & Keluhan</th>
                                <th class="py-3.5 px-4 text-center">Tgl Kejadian</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                                <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                            @forelse($tickets as $ticket)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <!-- Nomor Tiket -->
                                    <td class="py-4 px-4 sm:px-6 font-mono font-bold text-indigo-600 whitespace-nowrap">
                                        #TK-{{ str_pad((string)$ticket->id, 5, '0', STR_PAD_LEFT) }}
                                    </td>

                                    <!-- Nama Pelapor -->
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-slate-900">{{ $ticket->user->name ?? 'User Anonim' }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $ticket->user->email ?? '-' }}</div>
                                    </td>

                                    <!-- Acara Terkait -->
                                    <td class="py-4 px-4">
                                        @if($ticket->event)
                                            <div class="font-semibold text-slate-800 line-clamp-1 max-w-xs" title="{{ $ticket->event->title }}">
                                                {{ $ticket->event->title }}
                                            </div>
                                        @else
                                            <span class="text-slate-400 italic">Umum (Tanpa Acara Spesifik)</span>
                                        @endif
                                    </td>

                                    <!-- Subjek & Keluhan -->
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-slate-900 line-clamp-1">{{ $ticket->subject }}</div>
                                        <div class="text-slate-500 text-[11px] line-clamp-1 mt-0.5">{{ $ticket->description }}</div>
                                    </td>

                                    <!-- Tanggal Kejadian -->
                                    <td class="py-4 px-4 text-center whitespace-nowrap">
                                        <div class="font-semibold text-slate-700">
                                            {{ $ticket->incident_date ? \Carbon\Carbon::parse($ticket->incident_date)->format('d M Y') : '-' }}
                                        </div>
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="py-4 px-4 text-center whitespace-nowrap">
                                        @if($ticket->isCompleted())
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Selesai
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                Menunggu Respon
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Tombol Tindak Lanjut -->
                                    <td class="py-4 px-4 sm:px-6 text-right whitespace-nowrap">
                                        <a href="{{ route('admin.tickets.show', $ticket->id) }}" 
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition-colors">
                                            <span>Tindak Lanjut</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-500">
                                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="font-semibold text-slate-700">Tidak ada tiket pengaduan yang ditemukan</p>
                                        <p class="text-xs text-slate-400 mt-1">Belum ada peserta yang mengajukan kendala pada kriteria ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($tickets->hasPages())
                    <div class="p-4 bg-slate-50 border-t border-slate-200">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
