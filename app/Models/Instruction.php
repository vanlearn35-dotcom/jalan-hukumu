<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instruction extends Model
{
    protected $fillable = [
        'package_id',
        'section',
        'part',
        'content_html',
        'order',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
