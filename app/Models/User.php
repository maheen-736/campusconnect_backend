<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // We will need this for the React frontend!
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // A user has one specific role
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    // If the user is a society head, this grabs their society
    public function society(): HasOne
    {
        return $this->hasOne(Society::class, 'head_user_id');
    }

    // The events a student has registered for
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }
}
