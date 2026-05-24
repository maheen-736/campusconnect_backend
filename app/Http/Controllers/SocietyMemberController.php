<?php

namespace App\Http\Controllers;

use App\Models\SocietyMember;
use App\Models\Society;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SocietyMemberController extends Controller
{
    public function index($societyId)
    {
        $members = SocietyMember::where('society_id', $societyId)->get()->map(function ($m) {
            $m->photo_url = $m->photo ? asset('storage/' . $m->photo) : null;
            return $m;
        });
        return response()->json($members);
    }

    public function store(Request $request, $societyId)
    {
        $user = $request->user();
        if ($user->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'role'  => 'required|string|max:255',
            'email' => 'nullable|email',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('members', 'public');
        }

        $validated['society_id'] = $societyId;
        $member = SocietyMember::create($validated);
        $member->photo_url = $member->photo ? asset('storage/' . $member->photo) : null;

        return response()->json($member, 201);
    }

    public function destroy(Request $request, $societyId, $memberId)
    {
        $user = $request->user();
        if ($user->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $member = SocietyMember::where('society_id', $societyId)->findOrFail($memberId);
        if ($member->photo) Storage::disk('public')->delete($member->photo);
        $member->delete();

        return response()->json(['message' => 'Member removed.']);
    }
}