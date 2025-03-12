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
        // Check if user is authenticated first
        if (!Auth::check()) {
            return redirect()
                ->route('login')
                ->with('error', 'Please login to access this resource')
                ->setStatusCode(401);
        }

        // Get the authenticated user
        $user = Auth::user();

        // Get the event from route parameter
        $event = $request->route('event');

        // Handle case where event is not found or invalid
        if (!$event instanceof Event) {
            return redirect()
                ->route('events.index')
                ->with('error', 'Event not found')
                ->setStatusCode(404);
        }

        // Check if user is an organizer of the event
        if (!$event->organizers()->where('user_id', $user->id)->exists()) {
            return redirect()
                ->route('events.index')
                ->with('error', 'You are not authorized to perform this action')
                ->setStatusCode(403);
        }

        return $next($request);
    }
}
