<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CheckOrganizer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check authentication
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this resource');
        }

        $user = Auth::user();
        $eventId = $request->route('event');

        // Fetch the event if it's an ID
        $event = Event::find($eventId);
        if (!$event) {
            return redirect()->route('events.index')->with('error', 'Event not found');
        }

        // Check if user is an organizer
        if (!$event->organizers()->where('user_id', $user->id)->exists()) {
            return redirect()->route('events.index')->with('error', 'You are not authorized to perform this action');
        }

        return $next($request);
    }
}
