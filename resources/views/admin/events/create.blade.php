<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.events.index') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Tambah Acara & Webinar Baru
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Konfigurasikan jadwal, narasumber, token presensi ganda, materi, dan tautan live streaming BKN.
                </p>
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

                <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

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
                                   value="{{ old('title') }}"
                                   placeholder="Contoh: Sosialisasi Manajemen Talenta ASN dan Digitalisasi Layanan BKN"
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
                                      placeholder="Jelaskan gambaran umum, target kompetensi, dan silabus pemaparan..."
                                      class="w-full text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 py-2.5 px-3.5">{{ old('description') }}</textarea>
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
                                       value="{{ old('start_time') }}"
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
                                       value="{{ old('end_time') }}"
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
                                       value="{{ old('capacity', 100) }}"
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
                                           {{ in_array($speaker->id, old('speaker_ids', [])) ? 'checked' : '' }}
                                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                                    <div class="text-xs">
                                        <div class="font-bold text-slate-800">{{ $speaker->name }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $speaker->email }}</div>
                                    </div>
                                </label>
                            @empty
                                <div class="col-span-full text-center py-4 text-xs text-slate-400">
                                    Tidak ada narasumber (dosen) yang terdaftar. Akun pemateri dapat ditambahkan melalui manajemen user.
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

                        <div class="p-4 rounded-xl bg-indigo-50/70 border border-indigo-100 text-xs text-indigo-900 leading-relaxed mb-3">
                            Peserta harus mencatat <strong>Token Awal</strong> saat acara dimulai, dan <strong>Token Akhir</strong> saat kegiatan selesai. Kehadiran hanya terverifikasi jika selisih waktu memenuhi ambang batas minimal yang Anda tentukan di bawah ini.
                        </div>

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
                                       value="{{ old('start_token', $defaultStartToken ?? 'BKNSTART') }}"
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
                                       value="{{ old('end_token', $defaultEndToken ?? 'BKNDONE') }}"
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
                                       value="{{ old('attendance_threshold_minutes', 30) }}"
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

                        <!-- Link Live YouTube (Bisa diisi/diupdate kapan saja) -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="youtube_url" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Tautan Siaran Langsung YouTube Live (Opsional)
                                </label>
                                <span class="text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                    Dapat Ditambahkan Kapan Saja
                                </span>
                            </div>
                            <input type="url" 
                                   id="youtube_url" 
                                   name="youtube_url" 
                                   value="{{ old('youtube_url') }}"
                                   placeholder="https://www.youtube.com/watch?v=xxxxxxxx atau https://youtu.be/xxxx"
                                   class="w-full text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 py-2.5 px-3.5">
                            <p class="text-[11px] text-slate-500 mt-1">
                                Anda dapat mengosongkan ini terlebih dahulu jika link live YouTube baru tersedia menjelang jam acara, lalu memperbaruinya via menu Edit.
                            </p>
                        </div>

                        <!-- Upload File Materi Webinar -->
                        <div>
                            <label for="material_file" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Unggah Berkas Materi Paparan (Opsional)
                            </label>
                            <input type="file" 
                                   id="material_file" 
                                   name="material_file" 
                                   accept=".pdf,.ppt,.pptx,.zip,.rar"
                                   class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-300 rounded-xl p-1.5">
                            <p class="text-[11px] text-slate-400 mt-1">Format: PDF, PPT, PPTX, ZIP (Maksimal 20 MB).</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-6 border-t border-slate-200 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.events.index') }}" 
                           class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 font-bold text-xs sm:text-sm text-white shadow-sm shadow-indigo-200 transition-all">
                            Simpan & Publikasikan Acara
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</x-app-layout>
