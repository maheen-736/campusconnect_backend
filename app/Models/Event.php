<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title', 'description', 'event_date', 'venue',
        'capacity', 'society_id', 'image', 'recap',
        'recap_image', 'registration_url'
    ];

    public function society()
    {
        return $this->belongsTo(Society::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }
}