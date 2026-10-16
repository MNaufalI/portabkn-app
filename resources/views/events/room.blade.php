<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                            <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                            Ruang Webinar Interaktif
                        </span>
                    </div>
                    <h2 class="font-bold text-xl sm:text-2xl text-slate-800 leading-tight mt-1 line-clamp-1">
                        {{ $event->title }}
                    </h2>
                </div>
            </div>

            <!-- Quick Download Material Button in Header if Available -->
            @if($event->material_path)
                <a href="{{ route('events.material', $event->id) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition-all shrink-0">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Unduh Materi Webinar</span>
                </a>
            @endif
        </div>
    </x-slot>

    <!-- Main Container with Alpine.js Helpdesk Popover State -->
    <div x-data="{ helpdeskOpen: false }" class="py-8 bg-slate-50 min-h-screen relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- ============================================== -->
                <!-- LEFT COLUMN: LIVE STREAM PLAYER & DETAILS (8) -->
                <!-- ============================================== -->
                <div class="lg:col-span-8 space-y-6">
                    
                    <!-- YouTube Embed Player Container -->
                    <div class="bg-slate-900 rounded-3xl overflow-hidden shadow-2xl border border-slate-800">
                        @if ($isLate && ! $isOrganizer)
                            <!-- State Terkunci: Terlambat > 15 Menit Sejak Acara Dimulai -->
                            <div class="aspect-video w-full flex flex-col items-center justify-center p-8 text-center bg-gradient-to-br from-slate-950 via-slate-900 to-rose-950 text-white relative">
                                <div class="w-16 h-16 rounded-full bg-rose-500/20 border border-rose-500/30 flex items-center justify-center text-rose-500 mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg sm:text-xl font-bold tracking-tight text-white">Event Sedang Berjalan (Toleransi Waktu Berakhir)</h3>
                                <p class="text-xs sm:text-sm text-slate-300 mt-2 max-w-lg leading-relaxed">
                                    Anda telah melewati batas toleransi keterlambatan (15 menit sejak acara dimulai) tanpa mencatat presensi sesi awal. Siaran langsung dan presensi dinonaktifkan.
                                </p>
                                <div class="mt-4">
                                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 transition-colors">
                                        &larr; Kembali ke Katalog Acara
                                    </a>
                                </div>
                            </div>
                        @elseif ($event->youtube_embed_url)
                            <div class="relative aspect-video w-full">
                                <iframe src="{{ $event->youtube_embed_url }}?autoplay=1&rel=0&modestbranding=1" 
                                        title="{{ $event->title }}"
                                        class="absolute inset-0 w-full h-full"
                                        frameborder="0" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                        allowfullscreen>
                                </iframe>
                            </div>
                        @else
                            <!-- Placeholder Siaran Live Streaming -->
                            <div class="aspect-video w-full flex flex-col items-center justify-center p-8 text-center bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-white relative">
                                <div class="w-20 h-20 rounded-full bg-rose-600/20 border border-rose-500/30 flex items-center justify-center text-rose-500 mb-4 animate-pulse">
                                    <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold tracking-tight">Siaran Langsung Webinar BKN</h3>
                                <p class="text-xs text-slate-400 mt-2 max-w-md">
                                    Tautan streaming siaran langsung belum dikonfigurasikan atau akan aktif beberapa saat sebelum kegiatan dimulai.
                                </p>
                                @if($isOrganizer)
                                    <div class="mt-4">
                                        <a href="{{ route('admin.events.edit', $event->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white/10 hover:bg-white/20 text-white border border-white/20 transition-colors">
                                            <span>Masukkan URL YouTube (Admin)</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Video Title & Live Info Bar -->
                        <div class="p-5 bg-slate-900 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-t border-slate-800">
                            <div>
                                <h1 class="font-bold text-lg text-white leading-snug">{{ $event->title }}</h1>
                                <p class="text-xs text-slate-400 mt-1">
                                    Jadwal: {{ $event->start_time ? $event->start_time->format('l, d F Y • H:i') : '-' }} s/d {{ $event->end_time ? $event->end_time->format('H:i') : '-' }} WIB
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                    Peserta Terdaftar
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Materi Webinar & Paparan Narasumber Card -->
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-base text-slate-800">Materi & Modul Pembelajaran</h3>
                                    <p class="text-xs text-slate-500">Materi presentasi dan dokumen rujukan resmi dari narasumber BKN.</p>
                                </div>
                            </div>
                        </div>

                        @if ($event->material_path)
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-xs uppercase">
                                        PDF
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-slate-800">Bahan Paparan Webinar BKN</div>
                                        <div class="text-[11px] text-slate-400">Tersedia untuk seluruh peserta terdaftar</div>
                                    </div>
                                </div>

                                <a href="{{ route('events.material', $event->id) }}" 
                                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm shadow-indigo-200 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    <span>Unduh Materi</span>
                                </a>
                            </div>
                        @else
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-center text-xs text-slate-500">
                                <p>Bahan paparan materi webinar belum diunggah untuk sesi ini.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Detail & Deskripsi Acara -->
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-4">
                        <h3 class="font-bold text-base text-slate-800">Deskripsi & Pokok Bahasan</h3>
                        <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">
                            {{ $event->description ?: 'Tidak ada deskripsi rinci untuk kegiatan ini.' }}
                        </p>
                    </div>

                </div>

                <!-- ============================================== -->
                <!-- RIGHT COLUMN: DUAL-TOKEN PRESENSI PANEL (4)    -->
                <!-- ============================================== -->
                <div class="lg:col-span-4 space-y-6">
                    
                    <!-- Dual-Token Attendance Stepper Card -->
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-6">
                        <div>
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 mb-2">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                                Sistem Presensi Hibrida
                            </div>
                            <h3 class="font-bold text-lg text-slate-800 leading-tight">Presensi Dua Sesi</h3>
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                Kehadiran diverifikasi dengan mencocokkan selisih waktu antara <strong>Sesi Awal</strong> dan <strong>Sesi Akhir</strong> (Minimal {{ $event->attendance_threshold_minutes ?? 30 }} menit).
                            </p>
                        </div>

                        <!-- Info Token untuk Admin / Pemateri -->
                        @if ($isOrganizer)
                            <div class="p-3.5 bg-amber-50 rounded-2xl border border-amber-200 text-xs space-y-2">
                                <div class="font-bold text-amber-900 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                    </svg>
                                    Token Narasumber:
                                </div>
                                <div class="grid grid-cols-2 gap-2 font-mono">
                                    <div class="bg-white p-2 rounded-xl border border-amber-300">
                                        <div class="text-[10px] text-amber-700 font-sans font-semibold">Sesi Awal:</div>
                                        <div class="font-bold text-sm text-slate-800">{{ $event->start_token ?: $event->live_token }}</div>
                                    </div>
                                    <div class="bg-white p-2 rounded-xl border border-amber-300">
                                        <div class="text-[10px] text-amber-700 font-sans font-semibold">Sesi Akhir:</div>
                                        <div class="font-bold text-sm text-slate-800">{{ $event->end_token ?: $event->live_token }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Stepper Progress Presensi -->
                        <div class="space-y-4">
                            
                            <!-- STEP 1: SESI AWAL -->
                            <div class="p-4 rounded-2xl border {{ $registration?->pivot->checked_in_at ? 'bg-emerald-50/60 border-emerald-200' : 'bg-slate-50 border-slate-200' }} space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full {{ $registration?->pivot->checked_in_at ? 'bg-emerald-600 text-white' : 'bg-slate-300 text-slate-700' }} flex items-center justify-center font-bold text-xs">
                                            @if($registration?->pivot->checked_in_at) ✓ @else 1 @endif
                                        </div>
                                        <span class="text-xs font-bold text-slate-800">Sesi Awal (Check-In)</span>
                                    </div>
                                    @if($registration?->pivot->checked_in_at)
                                        <span class="text-[11px] font-semibold text-emerald-700">
                                            {{ \Carbon\Carbon::parse($registration->pivot->checked_in_at)->format('H:i') }} WIB
                                        </span>
                                    @endif
                                </div>

                                @if (! $registration?->pivot->checked_in_at)
                                    <form action="{{ route('events.presence') }}" method="POST" class="space-y-2">
                                        @csrf
                                        <input type="hidden" name="event_id" value="{{ $event->id }}">
                                        <input type="hidden" name="session_type" value="start">
                                        <div class="flex gap-2">
                                            <input type="text" 
                                                   name="live_token" 
                                                   required 
                                                   placeholder="Token Awal" 
                                                   class="w-full text-center font-mono font-bold text-xs uppercase py-2 px-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500">
                                            <button type="submit" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shrink-0">
                                                Kirim
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <p class="text-[11px] text-emerald-700">✓ Presensi awal tercatat. Jangan tutup sesi sebelum presensi akhir.</p>
                                @endif
                            </div>

                            <!-- STEP 2: DURASI MONITORING -->
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs flex items-center justify-between">
                                <div>
                                    <div class="font-semibold text-slate-700">Durasi Terhitung:</div>
                                    <div class="text-slate-500 text-[11px]">Ambang batas minimal: {{ $event->attendance_threshold_minutes ?? 30 }} menit</div>
                                </div>
                                <div class="font-extrabold text-sm text-indigo-700 font-mono">
                                    {{ $minutesAttended }} mnt
                                </div>
                            </div>

                            <!-- STEP 3: SESI AKHIR -->
                            <div class="p-4 rounded-2xl border {{ $registration?->pivot->is_attended ? 'bg-emerald-50/60 border-emerald-200' : ($registration?->pivot->checked_in_at ? 'bg-indigo-50/60 border-indigo-200' : 'bg-slate-50 border-slate-200 opacity-60') }} space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full {{ $registration?->pivot->is_attended ? 'bg-emerald-600 text-white' : 'bg-slate-300 text-slate-700' }} flex items-center justify-center font-bold text-xs">
                                            @if($registration?->pivot->is_attended) ✓ @else 2 @endif
                                        </div>
                                        <span class="text-xs font-bold text-slate-800">Sesi Akhir (Check-Out)</span>
                                    </div>
                                    @if($registration?->pivot->checked_out_at)
                                        <span class="text-[11px] font-semibold text-emerald-700">
                                            {{ \Carbon\Carbon::parse($registration->pivot->checked_out_at)->format('H:i') }} WIB
                                        </span>
                                    @endif
                                </div>

                                @if ($registration?->pivot->is_attended)
                                    <div class="space-y-2">
                                        <p class="text-[11px] text-emerald-700 font-medium">✓ Kehadiran diverifikasi penuh dan memenuhi ambang batas waktu.</p>
                                        <a href="{{ route('events.certificate', $event->id) }}" 
                                           class="w-full flex items-center justify-center gap-2 py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span>Unduh Sertifikat PDF</span>
                                        </a>
                                    </div>
                                @elseif ($registration?->pivot->checked_in_at)
                                    <form action="{{ route('events.presence') }}" method="POST" class="space-y-2">
                                        @csrf
                                        <input type="hidden" name="event_id" value="{{ $event->id }}">
                                        <input type="hidden" name="session_type" value="end">
                                        <div class="flex gap-2">
                                            <input type="text" 
                                                   name="live_token" 
                                                   required 
                                                   placeholder="Token Akhir" 
                                                   class="w-full text-center font-mono font-bold text-xs uppercase py-2 px-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500">
                                            <button type="submit" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shrink-0">
                                                Selesai
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <p class="text-[11px] text-slate-400">Harap lakukan presensi sesi awal terlebih dahulu.</p>
                                @endif
                            </div>

                        </div>
                    </div>

                    <!-- Narasumber Card -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
                        <h4 class="font-bold text-xs uppercase tracking-wider text-slate-500">Narasumber Kegiatan</h4>
                        <div class="space-y-3">
                            @forelse($event->speakers as $speaker)
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm shrink-0">
                                        {{ substr($speaker->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-xs text-slate-800">{{ $speaker->name }}</div>
                                        <div class="text-[11px] text-slate-400">Pakar Kepegawaian BKN RI</div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 italic">Tim Narasumber BKN RI</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- MODUL LIVEWIRE Q&A (TANYA JAWAB)           -->
                    <!-- ========================================== -->
                    <livewire:event-q-a :event="$event" :isOrganizer="$isOrganizer" />
                </div>

            </div>
        </div>

        <!-- ============================================================== -->
        <!-- FLOATING HELPDESK / CHAT TICKET BUTTON (POJOK KANAN BAWAH)     -->
        <!-- ============================================================== -->
        <div class="fixed bottom-6 right-6 z-50">
            
            <!-- Popover Menu Tiket Aduan -->
            <div x-show="helpdeskOpen" 
                 x-cloak
                 @click.away="helpdeskOpen = false"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                 class="mb-3 w-80 sm:w-96 bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden">
                
                <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-900 p-4 text-white flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center text-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-xs text-white">Pusat Bantuan & Pengaduan</h4>
                            <p class="text-[10px] text-blue-200">Helpdesk Kendala Kegiatan BKN</p>
                        </div>
                    </div>
                    <button type="button" @click="helpdeskOpen = false" class="text-white/60 hover:text-white p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-4 space-y-3">
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Mengalami kendala video streaming, materi, atau token presensi? Buat tiket aduan untuk ditindaklanjuti langsung oleh tim Admin BKN.
                    </p>

                    <a href="{{ route('tickets.create', ['event_id' => $event->id]) }}" 
                       class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-xl font-bold text-xs text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Buat Tiket Keluhan Baru</span>
                    </a>

                    <!-- Riwayat Tiket Acara Ini -->
                    @if($userTickets->isNotEmpty())
                        <div class="pt-2 border-t border-slate-100">
                            <div class="text-[11px] font-bold text-slate-500 uppercase mb-1.5">Tiket Anda Pada Acara Ini:</div>
                            <div class="space-y-1.5 max-h-36 overflow-y-auto pr-1">
                                @foreach($userTickets as $ticket)
                                    <a href="{{ route('tickets.show', $ticket->id) }}" class="p-2 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 flex items-center justify-between text-xs transition-colors">
                                        <span class="line-clamp-1 font-medium text-slate-800">{{ $ticket->subject }}</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold shrink-0 {{ $ticket->isCompleted() ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                            {{ $ticket->status }}
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="text-center pt-1">
                        <a href="{{ route('tickets.index') }}" class="text-[11px] font-semibold text-indigo-600 hover:underline">
                            Lihat Seluruh Tiket Saya &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <!-- Floating Action Trigger Button -->
            <button type="button" 
                    @click="helpdeskOpen = ! helpdeskOpen" 
                    class="w-14 h-14 rounded-full bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white shadow-xl shadow-indigo-300 flex items-center justify-center transition-all hover:scale-105 active:scale-95 focus:outline-none focus:ring-4 focus:ring-indigo-300">
                <svg x-show="!helpdeskOpen" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                <svg x-show="helpdeskOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

    </div>
</x-app-layout>
