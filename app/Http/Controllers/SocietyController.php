<?php

namespace App\Http\Controllers;

use App\Models\Society;
use Illuminate\Http\Request;

class SocietyController extends Controller
{
    public function index()
    {
        $societies = Society::with(['members', 'events' => function ($q) {
            $q->orderBy('event_date', 'desc');
        }])->get()->map(function ($s) {
            $s->cover_image_url = $s->cover_image ? asset('storage/' . $s->cover_image) : null;
            $s->events->transform(function ($e) {
                $e->image_url = $e->image ? asset('storage/' . $e->image) : null;
                $e->recap_image_url = $e->recap_image ? asset('storage/' . $e->recap_image) : null;
                return $e;
            });
            $s->members->transform(function ($m) {
                $m->photo_url = $m->photo ? asset('storage/' . $m->photo) : null;
                return $m;
            });
            return $s;
        });
        return response()->json($societies);
    }

    public function show($id)
    {
        $society = Society::with(['members', 'events' => function ($q) {
            $q->orderBy('event_date', 'desc');
        }])->findOrFail($id);

        $society->cover_image_url = $society->cover_image ? asset('storage/' . $society->cover_image) : null;
        $society->events->transform(function ($e) {
            $e->image_url = $e->image ? asset('storage/' . $e->image) : null;
            $e->recap_image_url = $e->recap_image ? asset('storage/' . $e->recap_image) : null;
            return $e;
        });
        $society->members->transform(function ($m) {
            $m->photo_url = $m->photo ? asset('storage/' . $m->photo) : null;
            return $m;
        });

        return response()->json($society);
    }
}