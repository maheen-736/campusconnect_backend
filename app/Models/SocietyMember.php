<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocietyMember extends Model
{
    protected $fillable = ['society_id', 'name', 'role', 'email', 'photo'];

    public function society()
    {
        return $this->belongsTo(Society::class);
    }
}