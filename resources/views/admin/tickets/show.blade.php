<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.tickets.index') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100">
                            #TK-{{ str_pad((string)$ticket->id, 5, '0', STR_PAD_LEFT) }}
                        </span>
                        @if($ticket->isCompleted())
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Selesai (Completed)
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                Menunggu Respon
                            </span>
                        @endif
                    </div>
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight mt-1">
                        {{ $ticket->subject }}
                    </h2>
                </div>
            </div>

            <div class="text-xs text-slate-500">
                Diajukan pada: {{ $ticket->created_at ? $ticket->created_at->format('d M Y, H:i') : '-' }} WIB
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                
                <!-- LEFT COLUMN: DETAIL PENGADUAN PESERTA (2 Kolom) -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Kartu Rincian Keluhan Peserta -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div>
                                <span class="text-xs text-slate-400 uppercase font-bold tracking-wider">Subjek Pengaduan</span>
                                <h3 class="font-extrabold text-lg text-slate-900 mt-0.5">{{ $ticket->subject }}</h3>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-slate-400 uppercase font-bold tracking-wider">Tanggal Kejadian</span>
                                <div class="font-bold text-slate-800 text-sm mt-0.5">
                                    {{ $ticket->incident_date ? \Carbon\Carbon::parse($ticket->incident_date)->format('d F Y') : '-' }}
                                </div>
                            </div>
                        </div>

                        <!-- Uraian Keluhan -->
                        <div>
                            <h4 class="text-xs text-slate-500 uppercase font-bold tracking-wider mb-2">Penjelasan Kendala</h4>
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-sm text-slate-800 leading-relaxed whitespace-pre-line">
                                {{ $ticket->description }}
                            </div>
                        </div>

                        <!-- Acara Terkait -->
                        @if($ticket->event)
                            <div class="p-4 rounded-xl bg-indigo-50/60 border border-indigo-100 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="text-[11px] text-indigo-600 font-semibold uppercase">Kegiatan Terkait:</span>
                                        <div class="font-bold text-sm text-slate-900">{{ $ticket->event->title }}</div>
                                    </div>
                                </div>
                                <a href="{{ route('events.room', $ticket->event->id) }}" 
                                   target="_blank"
                                   class="text-xs font-bold text-indigo-600 hover:text-indigo-800 underline shrink-0">
                                    Buka Ruang &rarr;
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Formulir Tindak Lanjut & Tanggapan Admin -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-4">
                        <h3 class="font-bold text-base text-slate-900 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-700 flex items-center justify-center text-xs font-extrabold">✓</span>
                            Tindak Lanjut & Keputusan Solusi Helpdesk
                        </h3>
                        <p class="text-xs text-slate-500">
                            Masukkan tanggapan atau instruksi solusi untuk peserta. Tanggapan ini akan langsung tampil di halaman detail tiket peserta.
                        </p>

                        <form action="{{ route('admin.tickets.update', $ticket->id) }}" method="POST" class="space-y-4 pt-2">
                            @csrf
                            @method('PATCH')

                            <!-- Pilihan Status Tiket -->
                            <div>
                                <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Status Tiket <span class="text-rose-500">*</span>
                                </label>
                                <select id="status" 
                                        name="status" 
                                        required
                                        class="w-full text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 py-2.5 px-3">
                                    <option value="pending" {{ old('status', $ticket->status) === 'pending' ? 'selected' : '' }}>
                                        🟡 Masih Pending (Menunggu Verifikasi / Investigasi)
                                    </option>
                                    <option value="completed" {{ old('status', $ticket->status) === 'completed' ? 'selected' : '' }}>
                                        🟢 Selesai (Completed - Solusi Telah Diberikan)
                                    </option>
                                </select>
                            </div>

                            <!-- Catatan Solusi / Tanggapan Admin -->
                            <div>
                                <label for="admin_notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Catatan / Jawaban Solusi Admin BKN <span class="text-rose-500">*</span>
                                </label>
                                <textarea id="admin_notes" 
                                          name="admin_notes" 
                                          rows="5" 
                                          required 
                                          placeholder="Contoh: Presensi kehadiran Anda telah kami verifikasi secara manual. Silakan muat ulang halaman ruang acara untuk mengunduh sertifikat..."
                                          class="w-full text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 py-2.5 px-3.5">{{ old('admin_notes', $ticket->admin_notes) }}</textarea>
                            </div>

                            <div class="pt-3 flex items-center justify-end">
                                <button type="submit" 
                                        class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 font-bold text-xs sm:text-sm text-white shadow-sm shadow-indigo-200 transition-all">
                                    Simpan Tanggapan & Perbarui Status
                                </button>
                            </div>
                        </form>
                    </div>

                </div>

                <!-- RIGHT COLUMN: INFORMASI PESERTA / PELAPOR (1 Kolom) -->
                <div class="space-y-6">
                    
                    <!-- Profil Pelapor -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                        <h4 class="font-bold text-xs uppercase tracking-wider text-slate-500">Data Peserta Pelapor</h4>
                        
                        <div class="flex items-center gap-3 pt-1">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 font-bold text-lg flex items-center justify-center shrink-0">
                                {{ substr($ticket->user->name ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <div class="font-extrabold text-sm text-slate-900">{{ $ticket->user->name ?? 'User Anonim' }}</div>
                                <div class="text-xs text-slate-500">{{ $ticket->user->email ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-slate-500">ID User:</span>
                                <span class="font-mono font-bold text-slate-800">#{{ $ticket->user_id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Status Akun:</span>
                                <span class="font-semibold text-emerald-600">Terdaftar</span>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Penyelesaian -->
                    @if($ticket->isCompleted())
                        <div class="bg-emerald-50 rounded-2xl border border-emerald-200 p-5 space-y-2">
                            <div class="flex items-center gap-2 text-emerald-800 font-bold text-xs">
                                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Tiket Telah Diselesaikan
                            </div>
                            <p class="text-[11px] text-emerald-700 leading-relaxed">
                                Diselesaikan oleh: <strong>{{ $ticket->resolver->name ?? 'Admin BKN' }}</strong><br>
                                Pada: {{ $ticket->resolved_at ? \Carbon\Carbon::parse($ticket->resolved_at)->format('d M Y, H:i') : '-' }} WIB
                            </p>
                        </div>
                    @endif

                </div>

            </div>

        </div>
    </div>
</x-app-layout>
