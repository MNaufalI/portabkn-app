<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    {{ __('Katalog Kegiatan & Presensi BKN') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Pusat pengembangan kompetensi ASN, presensi hibrida, dan sertifikasi Badan Kepegawaian Negara.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Sistem Presensi Siap
                </span>
            </div>
        </div>
    </x-slot>

    <!-- Container Utama dengan Alpine.js State untuk Modal Presensi -->
    <div x-data="{ 
            modalOpen: false, 
            activeEventId: null, 
            activeEventTitle: '',
            openModal(id, title) {
                this.activeEventId = id;
                this.activeEventTitle = title;
                this.modalOpen = true;
                this.$nextTick(() => {
                    const el = this.$refs.tokenInput;
                    if (el) { el.value = ''; el.focus(); }
                });
            },
            closeModal() {
                this.modalOpen = false;
                this.activeEventId = null;
                this.activeEventTitle = '';
            }
         }"
         @keydown.escape.window="closeModal()"
         class="py-8 bg-slate-50/70 min-h-screen">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <!-- Hero Banner & Stats Section -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-indigo-950 to-blue-900 text-white shadow-xl">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
                <div class="absolute -right-10 -bottom-10 w-80 h-80 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative p-6 sm:p-10 z-10">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                        <div class="lg:col-span-7 space-y-4">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/10 backdrop-blur-md text-blue-200 border border-white/15">
                                <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                Badan Kepegawaian Negara Republik Indonesia
                            </div>
                            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-white leading-tight">
                                Halo, {{ Auth::user()->name }}! 👋
                            </h1>
                            <p class="text-slate-300 text-sm sm:text-base max-w-xl leading-relaxed">
                                Hadiri webinar dan sharing session ASN. Masukkan <strong>Live Token</strong> saat acara berlangsung untuk memvalidasi kehadiran dan mengunduh sertifikat penghargaan resmi BKN.
                            </p>
                        </div>

                        <!-- Mini Stats Grid -->
                        <div class="lg:col-span-5 grid grid-cols-3 gap-3">
                            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/15 text-center flex flex-col justify-center">
                                <div class="text-2xl sm:text-3xl font-extrabold text-white">{{ $stats['total'] ?? 0 }}</div>
                                <div class="text-xs text-slate-300 font-medium mt-1">Total Acara</div>
                            </div>
                            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/15 text-center flex flex-col justify-center">
                                <div class="text-2xl sm:text-3xl font-extrabold text-blue-300">{{ $stats['registered'] ?? 0 }}</div>
                                <div class="text-xs text-slate-300 font-medium mt-1">Terdaftar</div>
                            </div>
                            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/15 text-center flex flex-col justify-center">
                                <div class="text-2xl sm:text-3xl font-extrabold text-emerald-400">{{ $stats['attended'] ?? 0 }}</div>
                                <div class="text-xs text-slate-300 font-medium mt-1">Hadir & Sertifikat</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Toolbar: Pencarian & Filter Status -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80">
                <form action="{{ route('dashboard') }}" method="GET" class="flex flex-col md:flex-row gap-4 justify-between items-stretch md:items-center">
                    
                    <!-- Search Input -->
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Cari judul webinar, topik, atau pemateri..."
                               class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        @if(request('search'))
                            <a href="{{ route('dashboard', ['filter' => request('filter')]) }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </a>
                        @endif
                    </div>

                    <!-- Filter Tabs -->
                    @php
                        $currentFilter = request('filter', 'all');
                    @endphp
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 scrollbar-none">
                        <input type="hidden" name="filter" id="filter-input" value="{{ $currentFilter }}">
                        
                        <a href="{{ route('dashboard', array_merge(request()->except('filter', 'page'), ['filter' => 'all'])) }}" 
                           class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all whitespace-nowrap {{ $currentFilter === 'all' ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Semua Kegiatan
                        </a>

                        <a href="{{ route('dashboard', array_merge(request()->except('filter', 'page'), ['filter' => 'upcoming'])) }}" 
                           class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all whitespace-nowrap {{ $currentFilter === 'upcoming' ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Akan Datang
                        </a>

                        <a href="{{ route('dashboard', array_merge(request()->except('filter', 'page'), ['filter' => 'registered'])) }}" 
                           class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all whitespace-nowrap {{ $currentFilter === 'registered' ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Kegiatan Saya
                        </a>

                        <a href="{{ route('dashboard', array_merge(request()->except('filter', 'page'), ['filter' => 'past'])) }}" 
                           class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all whitespace-nowrap {{ $currentFilter === 'past' ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Telah Selesai
                        </a>
                    </div>
                </form>
            </div>

            <!-- Grid Container Acara -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($events as $event)
                    @php
                        $userReg = $userRegistrations->get($event->id);
                        $isRegistered = $userReg !== null;
                        $isAttended = $isRegistered && (bool) $userReg->pivot->is_attended;
                        $attendedAt = $isAttended && $userReg->pivot->attended_at 
                            ? \Carbon\Carbon::parse($userReg->pivot->attended_at)->format('d M, H:i') 
                            : null;

                        $capacity = (int) ($event->capacity ?? 50);
                        $registeredCount = (int) ($event->registered_users_count ?? 0);
                        $percentage = $capacity > 0 ? min(100, round(($registeredCount / $capacity) * 100)) : 0;
                        $isFull = $registeredCount >= $capacity;
                        $isFinished = (bool) $event->is_finished;
                        $isSpeakerOrAdmin = Auth::user()->hasRole('admin') || Auth::user()->hasRole('dosen') || $event->speakers->contains('id', Auth::id());
                    @endphp

                    <!-- Card Komponen Event Modern -->
                    <div class="group bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col h-full overflow-hidden">
                        
                        <!-- Header Top Accent / Banner -->
                        <div class="h-3 bg-gradient-to-r {{ $isAttended ? 'from-emerald-500 via-teal-500 to-green-400' : ($isFinished ? 'from-slate-400 to-slate-500' : ($isRegistered ? 'from-blue-600 to-indigo-600' : 'from-indigo-600 via-blue-600 to-cyan-500')) }}"></div>

                        <div class="p-6 flex flex-col flex-grow space-y-4">
                            
                            <!-- Badges: Status & Waktu -->
                            <div class="flex items-center justify-between gap-2">
                                @if ($isAttended)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Hadir Terverifikasi
                                    </span>
                                @elseif ($isRegistered)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                                        </svg>
                                        Terdaftar (Belum Presensi)
                                    </span>
                                @elseif ($isFinished)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Selesai
                                    </span>
                                @elseif ($isFull)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Kuota Penuh
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                                        Akan Datang
                                    </span>
                                @endif

                                <!-- Waktu Acara Relatif -->
                                <span class="text-xs font-medium text-slate-500 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $event->start_time ? $event->start_time->format('H:i') : '--:--' }} WIB
                                </span>
                            </div>

                            <!-- Judul Acara -->
                            <div>
                                <h3 class="font-bold text-lg text-slate-900 group-hover:text-indigo-600 transition-colors line-clamp-2 leading-snug" title="{{ $event->title }}">
                                    {{ $event->title }}
                                </h3>
                                @if($event->description)
                                    <p class="text-xs text-slate-500 mt-2 line-clamp-2 leading-relaxed">
                                        {{ $event->description }}
                                    </p>
                                @endif
                            </div>

                            <!-- Detail Jadwal & Tanggal -->
                            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-indigo-50 border border-indigo-100 flex flex-col items-center justify-center shrink-0">
                                    <span class="text-[10px] font-bold uppercase text-indigo-500 leading-none">
                                        {{ $event->start_time ? $event->start_time->format('M') : 'BKN' }}
                                    </span>
                                    <span class="text-sm font-extrabold text-indigo-700 leading-tight">
                                        {{ $event->start_time ? $event->start_time->format('d') : '--' }}
                                    </span>
                                </div>
                                <div class="text-xs text-slate-700">
                                    <div class="font-semibold text-slate-800">
                                        {{ $event->start_time ? $event->start_time->format('l, d F Y') : '-' }}
                                    </div>
                                    <div class="text-slate-500 text-[11px] mt-0.5">
                                        {{ $event->start_time ? $event->start_time->format('H:i') : '-' }} s/d {{ $event->end_time ? $event->end_time->format('H:i') : '-' }} WIB
                                    </div>
                                </div>
                            </div>

                            <!-- Pemateri / Narasumber -->
                            <div class="text-xs">
                                <span class="font-semibold text-slate-700 block mb-1.5 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Narasumber:
                                </span>
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse ($event->speakers as $speaker)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200/60">
                                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                            {{ $speaker->name }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-slate-400 italic">Tim Narasumber BKN RI</span>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Live Token Info Card (Khusus Admin / Pemateri / Dosen) -->
                            @if ($isSpeakerOrAdmin)
                                <div x-data="{ copied: false }" class="p-2.5 bg-amber-50 rounded-xl border border-amber-200 text-xs">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-semibold text-amber-900 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                                </svg>
                                                Live Token (Narasumber):
                                            </span>
                                            <code class="font-mono font-bold text-amber-950 bg-white px-2 py-0.5 rounded border border-amber-300">
                                                {{ $event->live_token }}
                                            </code>
                                        </div>
                                        <button type="button" 
                                                @click="navigator.clipboard.writeText('{{ $event->live_token }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                                class="px-2 py-1 text-[11px] font-semibold text-amber-900 bg-white hover:bg-amber-100 rounded border border-amber-300 transition-colors">
                                            <span x-show="!copied">Salin Token</span>
                                            <span x-show="copied" class="text-emerald-700 font-bold">✓ Tersalin!</span>
                                        </button>
                                    </div>
                                    <p class="text-[10px] text-amber-700 mt-1">
                                        Bagikan kode token ini ke peserta saat sesi siaran langsung berlangsung untuk absensi.
                                    </p>
                                </div>
                            @endif

                            <!-- Bagian Kuota & Aksi Presensi (Ditekan ke dasar kartu) -->
                            <div class="mt-auto pt-4 border-t border-slate-100 space-y-3">
                                
                                <!-- Progress Bar Kuota Peserta -->
                                <div>
                                    <div class="flex justify-between items-center text-xs mb-1.5">
                                        <span class="text-slate-500 font-medium">Kapasitas Kuota:</span>
                                        <span class="font-bold {{ $isFull ? 'text-rose-600' : 'text-slate-800' }}">
                                            {{ $registeredCount }} <span class="text-slate-400 font-normal">/ {{ $capacity }}</span>
                                            <span class="text-[11px] font-normal text-slate-500">({{ $percentage }}%)</span>
                                        </span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                        <div class="h-2 rounded-full transition-all duration-500 {{ $isFull ? 'bg-rose-500' : ($percentage >= 75 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                             style="width: {{ $percentage }}%">
                                        </div>
                                    </div>
                                </div>

                                <!-- Tombol Aksi Berdasarkan State FSM Presensi & Pendaftaran -->
                                <div>
                                    @if ($isAttended)
                                        <!-- State: Sudah Hadir & Tersertifikasi -->
                                        <div class="space-y-2">
                                            <div class="w-full py-2 px-3 flex items-center justify-between bg-emerald-50 text-emerald-800 rounded-xl text-xs font-semibold border border-emerald-200">
                                                <span class="flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                    Presensi Terverifikasi
                                                </span>
                                                <span class="text-[11px] text-emerald-600 font-normal">{{ $attendedAt }}</span>
                                            </div>

                                            <div class="grid grid-cols-2 gap-2">
                                                <a href="{{ route('events.room', $event->id) }}" 
                                                   class="flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl font-semibold text-xs text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition-all">
                                                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                    </svg>
                                                    Ruang Event
                                                </a>

                                                <a href="{{ route('events.certificate', $event->id) }}" 
                                                   class="flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl font-semibold text-xs text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm shadow-emerald-200 hover:shadow-md transition-all">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                    Sertifikat
                                                </a>
                                            </div>
                                        </div>

                                    @elseif ($isRegistered)
                                        <!-- State: Terdaftar Tapi Belum Presensi -->
                                        <div class="space-y-2">
                                            <div class="w-full py-1.5 px-3 flex items-center justify-center gap-1.5 bg-blue-50 text-blue-700 rounded-xl text-xs font-medium border border-blue-100">
                                                <span>✓ Terdaftar sebagai peserta</span>
                                            </div>

                                            <div class="flex flex-col gap-2">
                                                <a href="{{ route('events.room', $event->id) }}" 
                                                   class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl font-bold text-xs sm:text-sm text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-sm shadow-blue-200 hover:shadow-md transition-all">
                                                    <svg class="w-4 h-4 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                    </svg>
                                                    <span>Masuk Ruang Event</span>
                                                </a>

                                                <button type="button"
                                                        @click="openModal({{ $event->id }}, '{{ addslashes($event->title) }}')"
                                                        class="w-full flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-xl font-medium text-xs text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 transition-colors">
                                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                                    </svg>
                                                    Presensi Cepat via Token
                                                </button>
                                            </div>
                                        </div>

                                    @elseif ($isFinished)
                                        <!-- State: Acara Selesai -->
                                        <div class="w-full py-2.5 px-4 text-center bg-slate-100 text-slate-400 rounded-xl font-semibold text-xs sm:text-sm cursor-not-allowed border border-slate-200">
                                            Kegiatan Telah Selesai
                                        </div>

                                    @elseif ($isFull)
                                        <!-- State: Kuota Penuh -->
                                        <div class="w-full py-2.5 px-4 text-center bg-rose-50 text-rose-600 rounded-xl font-semibold text-xs sm:text-sm cursor-not-allowed border border-rose-200">
                                            Kuota Acara Penuh
                                        </div>

                                    @else
                                        <!-- State: Belum Terdaftar & Kuota Tersedia -->
                                        <form action="{{ route('events.register') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="event_id" value="{{ $event->id }}">
                                            <button type="submit" 
                                                    class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl font-semibold text-xs sm:text-sm text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 shadow-sm shadow-indigo-200 hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                <span>Daftar Sekarang</span>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>

                @empty
                    <!-- Empty State Modern & Informatif -->
                    <div class="col-span-full bg-white rounded-3xl p-12 text-center shadow-sm border border-slate-200/80">
                        <div class="w-20 h-20 mx-auto rounded-3xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 mb-4">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">Tidak Ada Kegiatan Ditemukan</h3>
                        <p class="mt-1 text-sm text-slate-500 max-w-md mx-auto">
                            @if(request('search'))
                                Tidak ada kegiatan yang cocok dengan kata kunci "<span class="font-semibold text-slate-700">{{ request('search') }}</span>".
                            @else
                                Belum ada agenda kegiatan atau webinar yang sesuai dengan kategori ini.
                            @endif
                        </p>
                        @if(request('search') || request('filter'))
                            <div class="mt-6">
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-indigo-600 text-white hover:bg-indigo-700 transition-colors shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Reset Pencarian & Filter
                                </a>
                            </div>
                        @endif
                    </div>
                @endforelse
            </div>

            <!-- UI Paginasi Modern -->
            <div class="mt-8">
                @if ($events->hasPages())
                    <div class="bg-white rounded-2xl px-6 py-4 shadow-sm border border-slate-200/80">
                        {{ $events->links() }}
                    </div>
                @endif
            </div>

        </div>

        <!-- ========================================== -->
        <!-- Modal Presensi Hibrida (Live Token Input) -->
        <!-- ========================================== -->
        <div x-show="modalOpen" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true">
            
            <!-- Backdrop Blur & Fade -->
            <div x-show="modalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                 @click="closeModal()">
            </div>

            <!-- Modal Content Box -->
            <div class="min-h-full flex items-center justify-center p-4 text-center sm:p-0">
                <div x-show="modalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                    
                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-900 p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/10 backdrop-blur-md text-blue-200 border border-white/15">
                                <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a5 5 0 00-5 5v2a2 2 0 00-2 2v5a2 2 0 002 2h10a2 2 0 002-2v-5a2 2 0 00-2-2H7V7a3 3 0 016 0v2h2V7a5 5 0 00-5-5z"/>
                                </svg>
                                Presensi Hibrida BKN
                            </div>
                            <button type="button" @click="closeModal()" class="text-white/60 hover:text-white rounded-lg p-1 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <h3 class="mt-3 text-lg font-bold leading-6 text-white" id="modal-title">
                            Verifikasi Kehadiran & Sertifikat
                        </h3>
                        <p class="mt-1 text-xs text-slate-300 line-clamp-1" x-text="activeEventTitle"></p>
                    </div>

                    <!-- Modal Form Body -->
                    <form action="{{ route('events.presence') }}" method="POST" class="p-6 space-y-5">
                        @csrf
                        <input type="hidden" name="event_id" :value="activeEventId">

                        <div class="bg-blue-50 border border-blue-200/80 rounded-2xl p-4 text-xs text-blue-800 leading-relaxed space-y-1">
                            <p class="font-semibold flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Instruksi Presensi:
                            </p>
                            <p class="text-blue-700 pl-5">
                                Masukkan kode <strong>Live Token</strong> yang dibagikan oleh narasumber atau moderator saat sesi berlangsung. Setelah kehadiran terverifikasi, sertifikat penghargaan akan diproses ke email Anda.
                            </p>
                        </div>

                        <div class="space-y-2">
                            <label for="live_token_input" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Kode Live Token:
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       name="live_token" 
                                       id="live_token_input"
                                       x-ref="tokenInput"
                                       required 
                                       maxlength="50"
                                       placeholder="Masukkan Token (misal: aB7x9K2p)"
                                       autocomplete="off"
                                       class="w-full text-center font-mono font-bold text-lg tracking-widest py-3 px-4 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-inner">
                            </div>
                            <p class="text-[11px] text-slate-400 text-center">
                                Pencocokan token diverifikasi secara instan dengan algoritma B-Tree Indexing.
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-3 pt-2">
                            <button type="button" 
                                    @click="closeModal()" 
                                    class="w-1/3 py-2.5 px-4 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="w-2/3 py-2.5 px-4 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 shadow-md shadow-indigo-200 rounded-xl transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Verifikasi Presensi</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>