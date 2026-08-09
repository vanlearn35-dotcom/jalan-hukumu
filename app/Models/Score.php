<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Score extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_session_id',
        'listening',
        'structure',
        'reading',
        'total',
    ];

    // ================= RELATIONS =================

    public function examSession()
    {
        return $this->belongsTo(ExamSession::class);
    }
}
