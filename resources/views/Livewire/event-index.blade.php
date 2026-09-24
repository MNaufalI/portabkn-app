<div>
    <div class="container mx-auto p-6 max-w-7xl">
        <div class="flex justify-between items-center mb-8 border-b pb-4">
            <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight">Portal Kegiatan BKN</h2>
        </div>

        @if (session('success'))
            <div class="p-4 mb-6 text-sm text-green-800 bg-green-50 rounded-lg shadow-sm border border-green-200">
                <span class="font-bold">Berhasil!</span> {{ session('success') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="p-4 mb-6 text-sm text-yellow-800 bg-yellow-50 rounded-lg shadow-sm border border-yellow-200">
                <span class="font-bold">Peringatan:</span> {{ session('warning') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse ($events as $event)
                <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-100 flex flex-col">
                    <div class="p-6 flex-grow">
                        <h3 class="text-xl font-bold text-gray-900 mb-2 leading-tight">{{ $event->title }}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $event->description }}</p>
                        
                        <div class="bg-blue-50 rounded-md p-3 text-sm text-gray-700 mb-4 border border-blue-100">
                            <p class="mb-1"><span class="font-semibold">Dimulai:</span> {{ $event->start_time->format('d M Y, H:i') }}</p>
                            <p><span class="font-semibold">Terdaftar:</span> {{ $event->attendees_count }} orang</p>
                        </div>
                    </div>

                    <div class="p-6 pt-0 bg-gray-50 border-t border-gray-100">
                        <form action="{{ route('events.register') }}" method="POST" class="mt-2">
                            @csrf
                            <input type="hidden" name="event_id" value="{{ $event->id }}">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-lg transition-colors duration-200 focus:ring-4 focus:ring-blue-300">
                                Daftar Sekarang
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full flex flex-col items-center justify-center py-12 text-gray-500">
                    <p class="text-lg font-medium">Belum ada jadwal kegiatan baru.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $events->links() }}
        </div>
    </div>
</div>