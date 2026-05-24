<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Society extends Model
{
    protected $fillable = [
        'name', 'description', 'tagline', 'founded_at',
        'head_user_id', 'cover_image',
        'instagram', 'facebook', 'linkedin', 'tiktok', 'twitter', 'whatsapp'
    ];

    public function head()
    {
        return $this->belongsTo(User::class, 'head_user_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function members()
    {
        return $this->hasMany(SocietyMember::class);
    }
}