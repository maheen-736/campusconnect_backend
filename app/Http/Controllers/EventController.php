<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('society')
            ->withCount('registrations')
            ->orderBy('event_date', 'asc')
            ->get();
        $events->transform(function ($event) {
            $event->image_url = $event->image ? asset('storage/' . $event->image) : null;
            $event->recap_image_url = $event->recap_image ? asset('storage/' . $event->recap_image) : null;
            return $event;
        });
        return response()->json($events);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->role_id !== 2 || !$user->society) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'event_date'       => 'required|date',
            'venue'            => 'required|string|max:255',
            'capacity'         => 'required|integer|min:1',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'registration_url' => 'nullable|url',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        $validated['society_id'] = $user->society->id;
        $event = Event::create($validated);
        $event->image_url = $event->image ? asset('storage/' . $event->image) : null;

        // Send email notifications to all students
        $this->notifyStudents($event);

        return response()->json(['message' => 'Event created successfully', 'event' => $event], 201);
    }

    private function notifyStudents(Event $event)
    {
        try {
            $students = User::where('role_id', 3)->get();
            $society = $event->society ?? \App\Models\Society::find($event->society_id);
            $eventUrl = 'http://localhost:5173/events/' . $event->id;
            $imageUrl = $event->image_url ?? '';
            $date = date('l, F j, Y', strtotime($event->event_date));
            $time = date('h:i A', strtotime($event->event_date));

            foreach ($students as $student) {
                $htmlBody = "
                <!DOCTYPE html>
                <html>
                <head>
                  <meta charset='utf-8'>
                  <meta name='viewport' content='width=device-width, initial-scale=1'>
                </head>
                <body style='margin:0;padding:0;background:#1C1917;font-family:Inter,sans-serif;'>
                  <div style='max-width:600px;margin:0 auto;background:#292524;'>
                    <div style='background:linear-gradient(135deg,#F97316,#EA580C);padding:2rem;text-align:center;'>
                      <h1 style='color:white;margin:0;font-size:1.5rem;font-weight:900;'>Campus Connect</h1>
                      <p style='color:rgba(255,255,255,0.8);margin:4px 0 0;font-size:0.85rem;'>New Event Alert</p>
                    </div>
                    " . ($imageUrl ? "<img src='{$imageUrl}' style='width:100%;max-height:300px;object-fit:cover;display:block;' alt='Event Poster'/>" : "") . "
                    <div style='padding:2rem;'>
                      <div style='background:rgba(249,115,22,0.1);border:1px solid rgba(249,115,22,0.2);border-radius:10px;padding:0.5rem 1rem;display:inline-block;margin-bottom:1rem;'>
                        <span style='color:#F97316;font-size:0.78rem;font-weight:700;'>" . ($society ? $society->name : 'Campus Society') . "</span>
                      </div>
                      <h2 style='color:#FFF7ED;margin:0 0 1rem;font-size:1.5rem;font-weight:900;'>{$event->title}</h2>
                      <p style='color:#A8A29E;line-height:1.7;margin-bottom:1.5rem;'>" . ($event->description ? substr($event->description, 0, 200) . '...' : 'A new event has been announced.') . "</p>
                      <table style='width:100%;border-collapse:collapse;margin-bottom:1.5rem;'>
                        <tr><td style='padding:0.6rem 0;border-bottom:1px solid #3C3836;color:#78716C;font-size:0.85rem;'>Date</td><td style='padding:0.6rem 0;border-bottom:1px solid #3C3836;color:#FFF7ED;font-weight:700;font-size:0.85rem;text-align:right;'>{$date}</td></tr>
                        <tr><td style='padding:0.6rem 0;border-bottom:1px solid #3C3836;color:#78716C;font-size:0.85rem;'>Time</td><td style='padding:0.6rem 0;border-bottom:1px solid #3C3836;color:#FFF7ED;font-weight:700;font-size:0.85rem;text-align:right;'>{$time}</td></tr>
                        <tr><td style='padding:0.6rem 0;color:#78716C;font-size:0.85rem;'>Venue</td><td style='padding:0.6rem 0;color:#FFF7ED;font-weight:700;font-size:0.85rem;text-align:right;'>{$event->venue}</td></tr>
                      </table>
                      <div style='text-align:center;'>
                        <a href='{$eventUrl}' style='display:inline-block;background:#F97316;color:white;text-decoration:none;padding:0.875rem 2.5rem;border-radius:12px;font-weight:800;font-size:0.95rem;'>View Event →</a>
                      </div>
                    </div>
                    <div style='background:#1C1917;padding:1.5rem;text-align:center;border-top:1px solid #3C3836;'>
                      <p style='color:#57534E;font-size:0.78rem;margin:0;'>Campus Connect — NUML University</p>
                      <p style='color:#3C3836;font-size:0.72rem;margin:4px 0 0;'>You received this because you are registered as a student.</p>
                    </div>
                  </div>
                </body>
                </html>";

                Mail::html($htmlBody, function ($message) use ($student, $event) {
                    $message->to($student->email, $student->name)
                        ->subject('New Event: ' . $event->title . ' — Campus Connect');
                });
            }
        } catch (\Exception $e) {
            \Log::error('Email notification failed: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $event = Event::with('society')->withCount('registrations')->findOrFail($id);
        $event->image_url = $event->image ? asset('storage/' . $event->image) : null;
        $event->recap_image_url = $event->recap_image ? asset('storage/' . $event->recap_image) : null;
        return response()->json($event);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        $event = Event::findOrFail($id);

        if ($user->role_id !== 2 || !$user->society || $user->society->id !== $event->society_id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'title'            => 'sometimes|string|max:255',
            'description'      => 'nullable|string',
            'event_date'       => 'sometimes|date',
            'venue'            => 'sometimes|string|max:255',
            'capacity'         => 'sometimes|integer|min:1',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'registration_url' => 'nullable|url',
        ]);

        if ($request->hasFile('image')) {
            if ($event->image) Storage::disk('public')->delete($event->image);
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        $event->update($validated);
        $event->image_url = $event->image ? asset('storage/' . $event->image) : null;

        return response()->json(['message' => 'Event updated successfully', 'event' => $event]);
    }

    public function addRecap(Request $request, $id)
    {
        $user = $request->user();
        if ($user->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'recap'       => 'required|string',
            'recap_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3048',
        ]);

        $event = Event::findOrFail($id);
        $event->recap = $request->recap;

        if ($request->hasFile('recap_image')) {
            if ($event->recap_image) Storage::disk('public')->delete($event->recap_image);
            $event->recap_image = $request->file('recap_image')->store('recaps', 'public');
        }

        $event->save();
        $event->recap_image_url = $event->recap_image ? asset('storage/' . $event->recap_image) : null;

        return response()->json(['message' => 'Recap added.', 'event' => $event]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $event = Event::findOrFail($id);

        if ($user->role_id !== 2 || !$user->society || $user->society->id !== $event->society_id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($event->image) Storage::disk('public')->delete($event->image);
        if ($event->recap_image) Storage::disk('public')->delete($event->recap_image);
        $event->delete();

        return response()->json(['message' => 'Event deleted successfully']);
    }

    public function attendees($id)
    {
        $event = Event::with(['registrations.user'])->findOrFail($id);
        return response()->json($event->registrations);
    }
}