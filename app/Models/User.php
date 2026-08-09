<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',        // admin | participant
        'is_active',   // approval admin
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // ================= RELATIONS =================

    public function examSessions()
    {
        return $this->hasMany(ExamSession::class);
    }

    public function scores()
    {
        return $this->hasManyThrough(
            Score::class,
            ExamSession::class
        );
    }

    // ================= HELPERS =================

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isParticipant()
    {
        return $this->role === 'participant';
    }
}
