<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('tickets.index') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Buat Tiket Pengaduan Kendala
                </h2>
                <p class="text-sm text-slate-500 mt-0.5">
                    Sampaikan keluhan terkait kehadiran, sertifikat, atau sistem siaran kegiatan BKN RI.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
                
                <!-- Info Banner -->
                <div class="mb-6 p-4 rounded-xl bg-blue-50/80 border border-blue-200 flex items-start gap-3">
                    <div class="p-1 rounded-lg bg-blue-100 text-blue-700 shrink-0 mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="text-xs text-blue-900 leading-relaxed">
                        <span class="font-semibold block text-sm">Petunjuk Pengajuan Tiket Helpdesk</span>
                        Setiap tiket aduan yang diajukan akan berstatus <strong class="font-bold">Pending</strong> dan otomatis tercatat di sistem admin BKN. Admin akan meninjau dan menyelesaikan kendala Anda secepatnya.
                    </div>
                </div>

                <form action="{{ route('tickets.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Subjek Kendala -->
                    <div>
                        <x-input-label for="subject" value="Subjek / Topik Kendala" class="font-semibold text-slate-700" />
                        <x-text-input id="subject" 
                                      name="subject" 
                                      type="text" 
                                      class="mt-1.5 block w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" 
                                      value="{{ old('subject') }}" 
                                      placeholder="Contoh: Token Presensi Sesi Akhir Tidak Merespons / Gagal Unduh Sertifikat" 
                                      required 
                                      autofocus />
                        <x-input-error :messages="$errors->get('subject')" class="mt-1.5" />
                    </div>

                    <!-- Acara / Kegiatan Terkait -->
                    <div>
                        <x-input-label for="event_id" value="Kegiatan / Webinar Terkait (Opsional)" class="font-semibold text-slate-700" />
                        <select id="event_id" 
                                name="event_id" 
                                class="mt-1.5 block w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">-- Bukan Kendala Acara Spesifik (Kendala Umum) --</option>
                            @foreach($userEvents as $userEvent)
                                <option value="{{ $userEvent->id }}" {{ (old('event_id', $selectedEventId) == $userEvent->id) ? 'selected' : '' }}>
                                    {{ $userEvent->title }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('event_id')" class="mt-1.5" />
                        <p class="text-[11px] text-slate-500 mt-1">Pilih kegiatan jika kendala berhubungan dengan absensi, link materi, atau siaran live suatu event.</p>
                    </div>

                    <!-- Tanggal Kejadian / Incident Date -->
                    <div>
                        <x-input-label for="incident_date" value="Tanggal Kejadian / Terjadi Kendala" class="font-semibold text-slate-700" />
                        <x-text-input id="incident_date" 
                                      name="incident_date" 
                                      type="date" 
                                      class="mt-1.5 block w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" 
                                      value="{{ old('incident_date', date('Y-m-d')) }}" 
                                      required />
                        <x-input-error :messages="$errors->get('incident_date')" class="mt-1.5" />
                        <p class="text-[11px] text-slate-500 mt-1">Tentukan tanggal saat permasalahan atau kegagalan sistem tersebut dialami.</p>
                    </div>

                    <!-- Keterangan / Deskripsi -->
                    <div>
                        <x-input-label for="description" value="Keterangan & Rincian Keluhan" class="font-semibold text-slate-700" />
                        <textarea id="description" 
                                  name="description" 
                                  rows="5" 
                                  class="mt-1.5 block w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm" 
                                  placeholder="Jelaskan secara rinci kendala yang Anda alami, seperti pesan kesalahan yang muncul, perangkat yang digunakan, atau detail kejadian..." 
                                  required>{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-1.5" />
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('tickets.index') }}" 
                           class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-100 transition-colors">
                            Batal
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 shadow-sm shadow-indigo-200 hover:shadow-md transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            <span>Kirim Laporan Kendala</span>
                        </button>
                    </div>
                </form>

            </div>

        </div>
    </div>
</x-app-layout>
