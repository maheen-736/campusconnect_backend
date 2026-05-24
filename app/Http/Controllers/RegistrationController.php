<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Event;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
        ]);

        $user = $request->user();

        $alreadyRegistered = Registration::where('user_id', $user->id)
            ->where('event_id', $request->event_id)
            ->exists();

        if ($alreadyRegistered) {
            return response()->json(['message' => 'Already registered for this event.'], 409);
        }

        $event = Event::findOrFail($request->event_id);
        $count = Registration::where('event_id', $request->event_id)->count();

        if ($count >= $event->capacity) {
            return response()->json(['message' => 'Event is full.'], 409);
        }

        $registration = Registration::create([
            'user_id'  => $user->id,
            'event_id' => $request->event_id,
        ]);

        return response()->json(['message' => 'Registered successfully.', 'registration' => $registration], 201);
    }

    public function myRegistrations(Request $request)
    {
        $registrations = Registration::with(['event.society'])
            ->where('user_id', $request->user()->id)
            ->get();

        $registrations->transform(function ($reg) {
            if ($reg->event && $reg->event->image) {
                $reg->event->image_url = asset('storage/' . $reg->event->image);
            }
            return $reg;
        });

        return response()->json($registrations);
    }

    public function eventAttendees($id)
    {
        $registrations = Registration::with('user')
            ->where('event_id', $id)
            ->get();
        return response()->json($registrations);
    }
}