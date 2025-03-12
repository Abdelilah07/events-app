<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function __construct()
    {
        // Apply authentication middleware to all methods
        $this->middleware('auth');
        // Apply checkOrganizer middleware to all methods except index and show
        $this->middleware('checkOrganizer')->except(['index', 'show']);
    }

    public function index()
    {
        $events = Event::with('users')->get();
        return view('events.index', compact('events'))->with('status', 200);
    }

    public function create()
    {
        return view('events.create')->with('status', 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_event' => 'required|date',
        ]);

        $event = Event::create($request->only(['title', 'description', 'date_event']));
        $event->users()->attach(Auth::id(), ['isOrganizer' => true]);

        return redirect()->route('events.index')->with('success', 'Event created successfully')->with('status', 201);
    }

    public function show(Event $event)
    {
        $event->load('users');
        return view('events.show', compact('event'))->with('status', 200);
    }

    public function edit(Event $event)
    {
        return view('events.edit', compact('event'))->with('status', 200);
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_event' => 'required|date',
        ]);

        $event->update($request->only(['title', 'description', 'date_event']));

        return redirect()->route('events.index')->with('success', 'Event updated successfully')->with('status', 200);
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index')->with('success', 'Event deleted successfully')->with('status', 200);
    }

    public function detachUser(Event $event, $userId)
    {
        $event->users()->detach($userId);
        return redirect()->route('events.show', $event)->with('success', 'User detached')->with('status', 200);
    }

    public function syncUsers(Request $request, Event $event)
    {
        $event->users()->sync($request->input('user_ids', []));
        return redirect()->route('events.show', $event)->with('success', 'Users synced')->with('status', 200);
    }
}