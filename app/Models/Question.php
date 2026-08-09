<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'package_id',
        'number',
        'section',
        'part',
        'number',
        'type',
        'content_html',
        'passage_html',
        'passage_group',
        'options',
        'answer_key',
        'audio_path',
        'cue_start',
        'cue_end',
        'score_weight',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    // Accessor untuk memudahkan Exam Engine memanggil audio
    public function getAudioFileAttribute() {
        return $this->package->audio_path;
    }
}
