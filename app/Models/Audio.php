<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audio extends Model
{
    protected $table = 'audios';
    protected $fillable = [
        'package_id', 
        'filename',
        'path',
        'version',
        'is_active'
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
