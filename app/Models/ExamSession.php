<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ExamSession extends Model
{
    protected $fillable = [
        'user_id',
        'package_id',
        'mode',
        'status',
        'current_section',
        'section_state',
        'started_at',
        'section_started_at',
        'finished_at',
        'remaining_time',
    ];

    protected $casts = [
        'section_state' => 'array',
        'started_at' => 'datetime',
        'section_started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    /* ================= RELATIONS ================= */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function score(): HasOne
    {
        return $this->hasOne(Score::class);
    }

    /* ================= ENGINE HELPERS ================= */

    public function isPreview(): bool
    {
        return $this->mode === 'preview';
    }

    public function isListening(): bool
    {
        return $this->current_section === 'listening';
    }

    public function getSectionState(string $section): array
    {
        return $this->section_state[$section] ?? [];
    }

    public function markSectionCompleted(string $section): void
    {
        $state = $this->section_state;
        $state[$section]['completed'] = true;
        $state[$section]['locked'] = true;

        $this->update(['section_state' => $state]);
    }

    /* ================= LISTENING ================= */

    public function getListeningCueIndex(): int
    {
        return $this->section_state['listening']['cue_index'] ?? 0;
    }

    public function advanceListeningCue(): void
    {
        $state = $this->section_state;
        $state['listening']['cue_index'] =
            ($state['listening']['cue_index'] ?? 0) + 1;

        $this->update(['section_state' => $state]);
    }
}
