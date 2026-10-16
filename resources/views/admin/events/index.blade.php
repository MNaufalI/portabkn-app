<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        Administrator BKN RI
                    </span>
                </div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight mt-1">
                    Manajemen Acara & Webinar
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Kelola jadwal kegiatan, kuota peserta, token presensi dua sesi, materi paparan, dan tautan live streaming.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.events.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm shadow-indigo-200 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Acara Baru</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Stats Bar -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-xs text-slate-500 font-medium">Total Seluruh Acara</div>
                        <div class="text-2xl font-extrabold text-slate-900 mt-1">{{ $stats['total'] ?? 0 }}</div>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-xs text-slate-500 font-medium">Akan Datang / Berlangsung</div>
                        <div class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $stats['upcoming'] ?? 0 }}</div>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-xs text-slate-500 font-medium">Acara Selesai</div>
                        <div class="text-2xl font-extrabold text-slate-600 mt-1">{{ $stats['finished'] ?? 0 }}</div>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Toolbar -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row gap-3 items-center justify-between">
                <form action="{{ route('admin.events.index') }}" method="GET" class="w-full sm:w-96 flex items-center gap-2">
                    <div class="relative w-full">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Cari judul kegiatan atau topik..."
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
                        <a href="{{ route('admin.events.index') }}" class="text-xs text-slate-500 hover:text-slate-800 underline shrink-0">
                            Reset
                        </a>
                    @endif
                </form>

                <div class="text-xs text-slate-500">
                    Menampilkan <span class="font-bold text-slate-700">{{ $events->count() }}</span> dari <span class="font-bold text-slate-700">{{ $events->total() }}</span> kegiatan
                </div>
            </div>

            <!-- Table of Events -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3.5 px-4 sm:px-6">Kegiatan / Topik</th>
                                <th class="py-3.5 px-4">Jadwal & Waktu</th>
                                <th class="py-3.5 px-4">Narasumber</th>
                                <th class="py-3.5 px-4 text-center">Kuota Peserta</th>
                                <th class="py-3.5 px-4 text-center">Token Presensi</th>
                                <th class="py-3.5 px-4 text-center">Live Streaming</th>
                                <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                            @forelse($events as $event)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <!-- Judul & Deskripsi -->
                                    <td class="py-4 px-4 sm:px-6">
                                        <div class="font-bold text-slate-900 line-clamp-1 text-sm">{{ $event->title }}</div>
                                        <div class="text-slate-500 text-[11px] mt-0.5 line-clamp-1">{{ $event->description ?: 'Tanpa deskripsi' }}</div>
                                        @if($event->material_path)
                                            <span class="inline-flex items-center gap-1 mt-1 text-[10px] font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                Materi Siap Diunduh
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Jadwal Waktu -->
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <div class="font-semibold text-slate-800">
                                            {{ $event->start_time ? $event->start_time->format('d M Y') : '-' }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 font-mono">
                                            {{ $event->start_time ? $event->start_time->format('H:i') : '--' }} - {{ $event->end_time ? $event->end_time->format('H:i') : '--' }} WIB
                                        </div>
                                    </td>

                                    <!-- Narasumber -->
                                    <td class="py-4 px-4">
                                        @forelse($event->speakers as $speaker)
                                            <div class="inline-flex items-center gap-1 font-medium text-slate-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                                {{ $speaker->name }}
                                            </div>
                                        @empty
                                            <span class="text-slate-400 italic">Belum ditentukan</span>
                                        @endforelse
                                    </td>

                                    <!-- Kuota Peserta -->
                                    <td class="py-4 px-4 text-center whitespace-nowrap">
                                        <div class="font-extrabold text-slate-800">
                                            {{ $event->registered_users_count ?? 0 }} <span class="font-normal text-slate-400">/ {{ $event->capacity }}</span>
                                        </div>
                                        <div class="text-[10px] text-slate-500">
                                            Ambang: {{ $event->attendance_threshold_minutes ?? 30 }} menit
                                        </div>
                                    </td>

                                    <!-- Token Presensi -->
                                    <td class="py-4 px-4 text-center whitespace-nowrap font-mono text-[11px]">
                                        <div class="space-y-1">
                                            <div class="bg-slate-100 px-2 py-0.5 rounded border border-slate-200" title="Token Sesi Awal">
                                                <span class="text-slate-400 font-sans text-[10px]">Awal:</span> <strong>{{ $event->start_token ?: '-' }}</strong>
                                            </div>
                                            <div class="bg-slate-100 px-2 py-0.5 rounded border border-slate-200" title="Token Sesi Akhir">
                                                <span class="text-slate-400 font-sans text-[10px]">Akhir:</span> <strong>{{ $event->end_token ?: '-' }}</strong>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Status Live Streaming YouTube -->
                                    <td class="py-4 px-4 text-center whitespace-nowrap">
                                        @if($event->youtube_url)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-600 animate-pulse"></span>
                                                YouTube Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                                Belum Ada Link
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Tombol Aksi -->
                                    <td class="py-4 px-4 sm:px-6 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            <!-- Masuk Ruang Event (Admin View) -->
                                            <a href="{{ route('events.room', $event->id) }}" 
                                               class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" 
                                               title="Tinjau Ruang Webinar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>

                                            <!-- Unduh Laporan Hadir (CSV) -->
                                            <a href="{{ route('admin.events.export', $event->id) }}" 
                                               class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                               title="Unduh Rekap Kehadiran (CSV)">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                            </a>

                                            <!-- Edit Acara -->
                                            <a href="{{ route('admin.events.edit', $event->id) }}" 
                                               class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                               title="Edit Acara & Token / Link YouTube">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>

                                            <!-- Hapus Acara -->
                                            <form action="{{ route('admin.events.destroy', $event->id) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus acara ini? Seluruh data pendaftaran dan presensi terkait akan ikut terhapus.')" 
                                                  class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                                                        title="Hapus Acara">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-500">
                                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="font-semibold text-slate-700">Belum ada kegiatan yang terdaftar</p>
                                        <p class="text-xs text-slate-400 mt-1">Gunakan tombol "Tambah Acara Baru" di atas untuk membuat webinar pertama.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($events->hasPages())
                    <div class="p-4 bg-slate-50 border-t border-slate-200">
                        {{ $events->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>