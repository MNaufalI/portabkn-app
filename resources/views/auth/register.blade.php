<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Daftar Akun Baru</h2>
        <p class="text-xs text-slate-500 mt-1">
            Pendaftaran peserta terbuka untuk seluruh ASN, CPNS, dan masyarakat umum.
        </p>
    </div>

    <!-- Error Session -->
    @if ($errors->has('error'))
        <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700">
            {{ $errors->first('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Nama Lengkap -->
        <div>
            <x-input-label for="name" :value="__('Nama Lengkap (Beserta Gelar)')" class="text-xs font-semibold text-slate-700" />
            <x-text-input id="name" 
                          class="block mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm" 
                          type="text" 
                          name="name" 
                          :value="old('name')" 
                          required 
                          autofocus 
                          autocomplete="name" 
                          placeholder="Dr. Ahmad Fauzi, M.Si" />
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
        </div>

        <!-- NIK / Identitas Pegawai -->
        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="nik" :value="__('NIK / Identitas (10-16 Digit)')" class="text-xs font-semibold text-slate-700" />
                <span class="text-[11px] text-slate-400">Terenkripsi AES-256</span>
            </div>
            <x-text-input id="nik" 
                          class="block mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm font-mono" 
                          type="text" 
                          name="nik" 
                          :value="old('nik')" 
                          required 
                          minlength="10" 
                          maxlength="16" 
                          pattern="[0-9]{10,16}"
                          autocomplete="off" 
                          placeholder="320101xxxxxxxxxx" />
            <x-input-error :messages="$errors->get('nik')" class="mt-1.5" />
            <p class="text-[11px] text-slate-400 mt-1">Digunakan untuk validasi penerbitan sertifikat resmi BKN.</p>
        </div>

        <!-- Alamat Email -->
        <div>
            <x-input-label for="email" :value="__('Alamat Email Aktif')" class="text-xs font-semibold text-slate-700" />
            <x-text-input id="email" 
                          class="block mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm" 
                          type="email" 
                          name="email" 
                          :value="old('email')" 
                          required 
                          autocomplete="username" 
                          placeholder="nama@instansi.go.id / nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <!-- Kata Sandi -->
        <div>
            <x-input-label for="password" :value="__('Kata Sandi')" class="text-xs font-semibold text-slate-700" />
            <x-text-input id="password" 
                          class="block mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                          type="password"
                          name="password"
                          required 
                          autocomplete="new-password" 
                          placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <!-- Konfirmasi Kata Sandi -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" class="text-xs font-semibold text-slate-700" />
            <x-text-input id="password_confirmation" 
                          class="block mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                          type="password"
                          name="password_confirmation" 
                          required 
                          autocomplete="new-password" 
                          placeholder="Ulangi kata sandi" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <!-- Tombol Daftar -->
        <div class="pt-2">
            <button type="submit" 
                    class="w-full py-2.5 px-4 rounded-xl font-semibold text-sm text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 shadow-md shadow-indigo-200 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex items-center justify-center gap-2">
                <span>{{ __('Daftar Sekarang') }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>
        </div>
    </form>

    <!-- Pemisah & Kembali ke Login -->
    <div class="mt-6 pt-6 border-t border-slate-100 text-center">
        <p class="text-xs text-slate-500 mb-2">
            Sudah memiliki akun PortaBKN?
        </p>

        <a href="{{ route('login') }}" 
           class="inline-flex items-center justify-center gap-1.5 text-xs sm:text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
            <span>{{ __('Masuk ke Akun Anda (Log In)') }}</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</x-guest-layout>
