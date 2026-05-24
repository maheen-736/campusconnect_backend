<?php

namespace App\Http\Controllers;

use App\Models\Society;
use App\Models\SocietyMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SocietyHeadController extends Controller
{
    private function getSociety($user)
    {
        return Society::where('head_user_id', $user->id)->first();
    }

    public function mySociety(Request $request)
    {
        $user = $request->user();
        $society = Society::with(['members', 'events' => function ($q) {
            $q->orderBy('event_date', 'desc');
        }])->where('head_user_id', $user->id)->first();

        if (!$society) {
            return response()->json(['message' => 'No society assigned.'], 404);
        }

        $society->events->transform(function ($e) {
            $e->image_url = $e->image ? asset('storage/' . $e->image) : null;
            $e->recap_image_url = $e->recap_image ? asset('storage/' . $e->recap_image) : null;
            return $e;
        });

        $society->members->transform(function ($m) {
            $m->photo_url = $m->photo ? asset('storage/' . $m->photo) : null;
            return $m;
        });

        $society->cover_image_url = $society->cover_image ? asset('storage/' . $society->cover_image) : null;

        return response()->json($society);
    }

    public function updateSociety(Request $request)
    {
        $user = $request->user();
        $society = $this->getSociety($user);

        if (!$society) return response()->json(['message' => 'No society assigned.'], 404);

        $request->validate([
            'description' => 'nullable|string',
            'tagline'     => 'nullable|string|max:255',
            'founded_at'  => 'nullable|string|max:100',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3048',
            'instagram'   => 'nullable|string|max:255',
            'facebook'    => 'nullable|string|max:255',
            'linkedin'    => 'nullable|string|max:255',
            'tiktok'      => 'nullable|string|max:255',
            'twitter'     => 'nullable|string|max:255',
            'whatsapp'    => 'nullable|string|max:255',
        ]);

        $fields = ['description', 'tagline', 'founded_at', 'instagram', 'facebook', 'linkedin', 'tiktok', 'twitter', 'whatsapp'];
        foreach ($fields as $field) {
            if ($request->filled($field)) $society->$field = $request->$field;
        }

        if ($request->hasFile('cover_image')) {
            if ($society->cover_image) Storage::disk('public')->delete($society->cover_image);
            $society->cover_image = $request->file('cover_image')->store('societies', 'public');
        }

        $society->save();
        $society->cover_image_url = $society->cover_image ? asset('storage/' . $society->cover_image) : null;

        return response()->json(['message' => 'Society updated.', 'society' => $society]);
    }

    public function addMember(Request $request)
    {
        $user = $request->user();
        $society = $this->getSociety($user);

        if (!$society) return response()->json(['message' => 'No society assigned.'], 404);

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'role'  => 'required|string|max:255',
            'email' => 'nullable|email',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('members', 'public');
        }

        $validated['society_id'] = $society->id;
        $member = SocietyMember::create($validated);
        $member->photo_url = $member->photo ? asset('storage/' . $member->photo) : null;

        return response()->json($member, 201);
    }

    public function removeMember(Request $request, $memberId)
    {
        $user = $request->user();
        $society = $this->getSociety($user);

        if (!$society) return response()->json(['message' => 'No society assigned.'], 404);

        $member = SocietyMember::where('society_id', $society->id)->findOrFail($memberId);
        if ($member->photo) Storage::disk('public')->delete($member->photo);
        $member->delete();

        return response()->json(['message' => 'Member removed.']);
    }
}