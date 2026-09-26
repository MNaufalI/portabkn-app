<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRegistrationRequest;
use App\Jobs\GenerateCertificateJob;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Exception;

class RegisterEventController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(StoreEventRegistrationRequest $request): RedirectResponse
    {
        try {
            $user = $request->user();
            $eventId = (int) $request->validated('event_id');
            $event = Event::findOrFail($eventId);
            $user->registeredEvents()->attach($eventId);
    
            GenerateCertificateJob::dispatch($user, $event);
            
            return redirect()->back()->with('success', 'Successfully registered for the event.');
        } catch (QueryException $e) {
            if ($e->errorInfo[1] === 1062) {
                return redirect()->back()->with('warning', 'You have already registered for this event.');
            }
        log::error('Database error during event registration: ' . $e->getMessage(),
        [
            'user_id' => $request->user()?->id,
            'event_id' => $request->validated('event_id'),
        ]);
        return redirect()->back()->with('error', 'Server error occurred. Please try again later.');
        } catch (\Exception $e) {
        log::critical('Unexpected error during event registration: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Server error occurred. Please try again later.');
        }
    }
}
