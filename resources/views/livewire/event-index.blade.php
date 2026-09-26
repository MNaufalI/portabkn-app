<div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h2 class="text-2xl font-extrabold text-gray-800">Daftar Kegiatan BKN</h2>
        </div>

        @if (session('success'))
            <div class="p-4 mb-6 text-sm text-green-800 bg-green-50 rounded-lg border border-green-200">
                <span class="font-bold">Berhasil!</span> {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($events as $event)
                <div class="border border-gray-200 rounded-xl p-5 hover:shadow-md transition-shadow flex flex-col">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $event->title }}</h3>
                    <p class="text-sm text-gray-600 mb-4 flex-grow">{{ Str::limit($event->description, 100) }}</p>
                    
                    <div class="bg-blue-50 text-blue-800 text-sm p-3 rounded mb-4 font-medium">
                        Terdaftar: {{ $event->attendees_count }} Peserta
                    </div>

                    <form action="{{ route('events.register') }}" method="POST">
                        @csrf
                        <input type="hidden" name="event_id" value="{{ $event->id }}">
                        <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition">
                            Daftar Sekarang
                        </button>
                    </form>
                </div>
            @empty
                <div class="col-span-full text-center py-8 text-gray-500">
                    Belum ada kegiatan BKN yang tersedia.
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $events->links() }}
        </div>
    </div>
</div>