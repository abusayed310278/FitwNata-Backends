<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgressJournal extends Model
{
    protected $guarded = [];

    protected $casts = [
        'entry_date' => 'date',
    ];

    /**
     * Get the user that owns the progress journal.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the coach that created the note (if it's a coach note).
     */
    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    /**
     * Scope for user entries (not coach notes).
     */
    public function scopeUserEntries($query)
    {
        return $query->where('entry_type', '!=', 'coach_note')
                    ->orWhereNull('entry_type');
    }

    /**
     * Scope for coach notes.
     */
    public function scopeCoachNotes($query)
    {
        return $query->where('entry_type', 'coach_note');
    }
}
