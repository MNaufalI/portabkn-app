<!-- 
  Menggunakan Alpine.js (x-data) untuk FSM (Finite State Machine) frontend.
  'show: true' adalah state awal. x-init akan mengeksekusi setTimeout untuk merubah state menjadi false setelah 3 detik.
-->
<div x-data="{ show: true }" 
     x-init="setTimeout(() => show = false, 3000)"
     x-show="show" 
     x-transition.duration.500ms
     class="fixed top-5 right-5 z-50 min-w-[300px]">

    {{-- Keamanan Pertama: Sintaks kurung kurawal ganda secara internal memanggil htmlspecialchars()... --}}

    @if (session()->has('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md" role="alert">
            <p class="font-bold">Sukses</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session()->has('warning'))
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded shadow-md" role="alert">
            <p class="font-bold">Peringatan</p>
            <p>{{ session('warning') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md" role="alert">
            <p class="font-bold">Gagal</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif

</div>