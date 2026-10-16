<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.events.index') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        Edit Acara: {{ $event->title }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                        Perbarui informasi kegiatan, jadwal, materi, token presensi, dan tautan live streaming YouTube BKN.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('events.room', $event->id) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-bold text-xs sm:text-sm text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition-all">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    <span>Tinjau Ruang Event &rarr;</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">

                <!-- Alert Validation Errors -->
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800 space-y-1">
                        <div class="font-bold flex items-center gap-1.5 text-sm">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Terdapat kesalahan pada input formulir:
                        </div>
                        <ul class="list-disc pl-5 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- 1. INFORMASI UTAMA KEGIATAN -->
                    <div class="space-y-4">
                        <h3 class="font-bold text-base text-slate-800 border-b border-slate-100 pb-2 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-indigo-50 text-indigo-700 flex items-center justify-center text-xs font-extrabold">1</span>
                            Informasi Utama Kegiatan
                        </h3>

                        <!-- Judul Acara -->
                        <div>
                            <label for="title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Nama / Judul Webinar <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   required 
                                   value="{{ old('title', $event->title) }}"
                                   class="w-full text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 py-2.5 px-3.5">
                        </div>

                        <!-- Deskripsi Acara -->
                        <div>
                            <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Deskripsi & Pokok Bahasan
                            </label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="3" 
                                      class="w-full text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 py-2.5 px-3.5">{{ old('description', $event->description) }}</textarea>
                        </div>
                    </div>

                    <!-- 2. JADWAL & KAPASITAS PESERTA -->
                    <div class="space-y-4 pt-2">
                        <h3 class="font-bold text-base text-slate-800 border-b border-slate-100 pb-2 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-indigo-50 text-indigo-700 flex items-center justify-center text-xs font-extrabold">2</span>
                            Jadwal Pelaksanaan & Kuota
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Waktu Mulai -->
                            <div>
                                <label for="start_time" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Waktu Mulai <span class="text-rose-500">*</span>
                                </label>
                                <input type="datetime-local" 
                                       id="start_time" 
                                       name="start_time" 
                                       required
                                       value="{{ old('start_time', $event->start_time ? $event->start_time->format('Y-m-d\TH:i') : '') }}"
                                       class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 py-2.5 px-3">
                            </div>

                            <!-- Waktu Selesai -->
                            <div>
                                <label for="end_time" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Waktu Selesai <span class="text-rose-500">*</span>
                                </label>
                                <input type="datetime-local" 
                                       id="end_time" 
                                       name="end_time" 
                                       required
                                       value="{{ old('end_time', $event->end_time ? $event->end_time->format('Y-m-d\TH:i') : '') }}"
                                       class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 py-2.5 px-3">
                            </div>

                            <!-- Kapasitas Maksimal -->
                            <div>
                                <label for="capacity" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Kapasitas Kuota <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" 
                                       id="capacity" 
                                       name="capacity" 
                                       required 
                                       min="1" 
                                       max="50000"
                                       value="{{ old('capacity', $event->capacity) }}"
                                       class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 py-2.5 px-3">
                            </div>
                        </div>
                    </div>

                    <!-- 3. NARASUMBER KEGIATAN -->
                    <div class="space-y-4 pt-2">
                        <h3 class="font-bold text-base text-slate-800 border-b border-slate-100 pb-2 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-indigo-50 text-indigo-700 flex items-center justify-center text-xs font-extrabold">3</span>
                            Pilih Narasumber / Pemateri BKN
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-48 overflow-y-auto p-2 border border-slate-200 rounded-xl bg-slate-50">
                            @forelse($potentialSpeakers as $speaker)
                                <label class="flex items-center gap-3 p-2 rounded-lg bg-white border border-slate-200 hover:border-indigo-300 cursor-pointer transition-colors">
                                    <input type="checkbox" 
                                           name="speaker_ids[]" 
                                           value="{{ $speaker->id }}"
                                           {{ in_array($speaker->id, old('speaker_ids', $selectedSpeakerIds)) ? 'checked' : '' }}
                                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                                    <div class="text-xs">
                                        <div class="font-bold text-slate-800">{{ $speaker->name }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $speaker->email }}</div>
                                    </div>
                                </label>
                            @empty
                                <div class="col-span-full text-center py-4 text-xs text-slate-400">
                                    Tidak ada narasumber (dosen) yang terdaftar.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- 4. TOKEN PRESENSI GANDA & AMBANG BATAS DURASI -->
                    <div class="space-y-4 pt-2">
                        <h3 class="font-bold text-base text-slate-800 border-b border-slate-100 pb-2 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-indigo-50 text-indigo-700 flex items-center justify-center text-xs font-extrabold">4</span>
                            Sistem Presensi Dua Sesi (Dual-Token Verification)
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Token Sesi Awal -->
                            <div>
                                <label for="start_token" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Token Sesi Awal (Check-In) <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" 
                                       id="start_token" 
                                       name="start_token" 
                                       required
                                       value="{{ old('start_token', $event->start_token ?: $event->live_token) }}"
                                       class="w-full text-xs font-mono font-bold uppercase rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 py-2.5 px-3 bg-slate-50">
                            </div>

                            <!-- Token Sesi Akhir -->
                            <div>
                                <label for="end_token" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Token Sesi Akhir (Check-Out) <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" 
                                       id="end_token" 
                                       name="end_token" 
                                       required
                                       value="{{ old('end_token', $event->end_token ?: $event->live_token) }}"
                                       class="w-full text-xs font-mono font-bold uppercase rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 py-2.5 px-3 bg-slate-50">
                            </div>

                            <!-- Ambang Batas Durasi Kehadiran -->
                            <div>
                                <label for="attendance_threshold_minutes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Ambang Batas Kehadiran (Menit) <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" 
                                       id="attendance_threshold_minutes" 
                                       name="attendance_threshold_minutes" 
                                       required
                                       min="0"
                                       value="{{ old('attendance_threshold_minutes', $event->attendance_threshold_minutes ?? 30) }}"
                                       class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 py-2.5 px-3">
                                <span class="text-[10px] text-slate-500 mt-1 block">Minimal durasi peserta menyimak materi.</span>
                            </div>
                        </div>
                    </div>

                    <!-- 5. TAUTAN YOUTUBE LIVE & FILE MATERI PAPARAN -->
                    <div class="space-y-4 pt-2">
                        <h3 class="font-bold text-base text-slate-800 border-b border-slate-100 pb-2 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-indigo-50 text-indigo-700 flex items-center justify-center text-xs font-extrabold">5</span>
                            Live Streaming & Materi Paparan
                        </h3>

                        <!-- Link Live YouTube (Dapat Diupdate Kapan Saja) -->
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <div class="flex items-center justify-between">
                                <label for="youtube_url" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Tautan YouTube Live Streaming
                                </label>
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                    ✓ Dapat Dimasukkan Kapan Saja Menjelang Acara
                                </span>
                            </div>
                            <input type="url" 
                                   id="youtube_url" 
                                   name="youtube_url" 
                                   value="{{ old('youtube_url', $event->youtube_url) }}"
                                   placeholder="Contoh: https://www.youtube.com/watch?v=xxxxxxx atau https://youtu.be/xxxx"
                                   class="w-full text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 py-2.5 px-3.5 bg-white">
                            <p class="text-[11px] text-slate-500">
                                Saat live streaming dimulai, tempelkan tautan video YouTube di sini lalu klik tombol simpan di bawah. Iframe live player di ruang event peserta akan langsung terhubung secara otomatis.
                            </p>
                        </div>

                        <!-- Upload File Materi Webinar -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="material_file" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Berkas Materi Paparan Webinar
                                </label>
                                @if($event->material_path)
                                    <span class="text-[11px] text-indigo-600 font-semibold">
                                        File saat ini tersimpan: {{ basename($event->material_path) }}
                                    </span>
                                @endif
                            </div>
                            <input type="file" 
                                   id="material_file" 
                                   name="material_file" 
                                   accept=".pdf,.ppt,.pptx,.zip,.rar"
                                   class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-300 rounded-xl p-1.5">
                            <p class="text-[11px] text-slate-400 mt-1">Kosongkan jika tidak ingin mengubah berkas materi yang sudah ada.</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-6 border-t border-slate-200 flex items-center justify-between">
                        <a href="{{ route('admin.events.index') }}" 
                           class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
                            Kembali ke Daftar Acara
                        </a>
                        <button type="submit" 
                                class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 font-bold text-xs sm:text-sm text-white shadow-sm shadow-indigo-200 transition-all">
                            Simpan Perubahan Acara
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</x-app-layout>
