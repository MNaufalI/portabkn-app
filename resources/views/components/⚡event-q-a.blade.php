<?php

use App\Models\Event;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\Validate;

new class extends Component {
    // ==========================================
    // BAGIAN PHP (OTAK/LOGIKA)
    // ==========================================
    
    public Event $event;

    #[Validate('required|string|min:5|max:1000')]
    public string $newQuestion = '';

    public bool $isOrganizer = false;

    public function mount(Event $event, bool $isOrganizer): void
    {
        $this->event = $event;
        $this->isOrganizer = $isOrganizer;
    }

    public function submitQuestion(): void
    {
        $this->validate();

        try {
            Question::create([
                'event_id' => $this->event->id,
                'user_id'  => Auth::id(),
                'body'     => trim($this->newQuestion),
            ]);

            $this->reset('newQuestion');
        } catch (\Throwable $e) {
            Log::error('Gagal menyimpan pertanyaan Q&A: ' . $e->getMessage());
            session()->flash('error', 'Gagal mengirim pertanyaan.');
        }
    }

    public function toggleAnswerStatus(int $questionId): void
    {
        if (! $this->isOrganizer) {
            return;
        }

        try {
            $question = Question::findOrFail($questionId);
            $question->update([
                'is_answered' => ! $question->is_answered
            ]);
        } catch (\Throwable $e) {
            Log::error('Gagal update status Q&A: ' . $e->getMessage());
        }
    }

    // Mengirim data ke HTML di bawahnya (Eager Loading Mencegah N+1)
    public function with(): array
    {
        return [
            'questions' => Question::with('user:id,name')
                ->where('event_id', $this->event->id)
                ->latest()
                ->get()
        ];
    }
}; ?>

<!-- ========================================== -->
<!-- BAGIAN HTML (TAMPILAN/BAJU)                -->
<!-- ========================================== -->
<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-6 flex flex-col h-[600px]">
    
    <!-- Bagian Header (Judul Kotak Chat) -->
    <div class="flex items-center justify-between border-b border-slate-100 pb-4 shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-base text-slate-800">Tanya Jawab (Q&A)</h3>
                <p class="text-[11px] text-slate-500">Diskusi interaktif bersama narasumber BKN.</p>
            </div>
        </div>
        <div class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-100">
            {{ $questions->count() }} Pertanyaan
        </div>
    </div>

    <!-- Bagian Tengah: Daftar Pertanyaan (Scrollable) -->
    <div class="flex-grow overflow-y-auto space-y-4 pr-2 scrollbar-thin scrollbar-thumb-slate-200 scrollbar-track-transparent">
        @forelse ($questions as $question)
            <div class="p-4 rounded-2xl {{ $question->is_answered ? 'bg-emerald-50/50 border border-emerald-100' : 'bg-slate-50 border border-slate-100' }} transition-colors">
                
                <div class="flex items-center justify-between gap-2 mb-2">
                    <!-- Nama Penanya -->
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-[10px] font-bold">
                            {{ substr($question->user->name, 0, 1) }}
                        </div>
                        <span class="text-xs font-bold text-slate-800">{{ $question->user->name }}</span>
                    </div>

                    <!-- Label Status / Tombol Centang untuk Admin -->
                    @if ($isOrganizer)
                        <button type="button" 
                                wire:click="toggleAnswerStatus({{ $question->id }})"
                                class="text-[10px] font-bold px-2 py-0.5 rounded-md border transition-colors focus:outline-none {{ $question->is_answered ? 'bg-emerald-100 text-emerald-700 border-emerald-200 hover:bg-slate-200 hover:text-slate-600 hover:border-slate-300' : 'bg-white text-slate-500 border-slate-300 hover:bg-emerald-100 hover:text-emerald-700 hover:border-emerald-200' }}">
                            {{ $question->is_answered ? '✓ Terjawab' : 'Tandai Terjawab' }}
                        </button>
                    @else
                        @if ($question->is_answered)
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                                ✓ Terjawab
                            </span>
                        @else
                            <span class="text-[10px] font-medium text-slate-400">
                                {{ $question->created_at->diffForHumans() }}
                            </span>
                        @endif
                    @endif
                </div>

                <!-- Isi Pertanyaan -->
                <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                    {{ $question->body }}
                </p>
            </div>
        @empty
            <div class="text-center py-10">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <p class="text-sm font-semibold text-slate-600">Belum ada diskusi</p>
                <p class="text-xs text-slate-400 mt-1">Jadilah yang pertama bertanya kepada narasumber!</p>
            </div>
        @endforelse
    </div>

    <!-- Bagian Bawah: Form Input (Ketikan) -->
    <div class="pt-4 border-t border-slate-100 shrink-0">
        <!-- Fitur pencegahan spam / validasi error dari Livewire -->
        @error('newQuestion')
            <span class="text-[10px] text-rose-500 font-semibold mb-1 block">{{ $message }}</span>
        @enderror

        <form wire:submit="submitQuestion" class="relative">
            <textarea wire:model="newQuestion" 
                      rows="2" 
                      placeholder="Ketik pertanyaan Anda di sini..."
                      class="w-full text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 py-3 pr-14 pl-4 bg-slate-50 resize-none shadow-inner"
                      required></textarea>

            <button type="submit" 
                    class="absolute right-2 bottom-2 p-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition-colors disabled:opacity-50"
                    wire:loading.attr="disabled"
                    wire:target="submitQuestion">
                
                <!-- Icon saat normal -->
                <svg wire:loading.remove wire:target="submitQuestion" class="w-5 h-5 transform rotate-90" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                </svg>

                <!-- Icon saat sedang memuat (loading) -->
                <svg wire:loading wire:target="submitQuestion" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>

            </button>
        </form>
        <p class="text-[10px] text-slate-400 mt-2 text-center">
            Pertanyaan Anda akan dibaca oleh narasumber dan panelis BKN.
        </p>
    </div>

</div>