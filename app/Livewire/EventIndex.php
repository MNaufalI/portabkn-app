<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Event;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class EventIndex extends Component
{
    use WithPagination;
    public function render(): View
    {
        try {
            $events = Event::withCount('attendees')
                ->where('start_time', '>=', now())
                ->orderBy('start_time', 'asc')
                ->paginate(6);

            return view('livewire.event-index', [
                'events' => $events
            ]);
        } catch (\Exception $e) {
            Log::error('Kegagalan render event: ' . $e->getMessage());
            return view('livewire.event-index', ['events' => collect()])
                ->with('error', 'Gagal memuat data dari server.');
        }
    }
}