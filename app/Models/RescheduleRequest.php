<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RescheduleRequest extends Model
{
    protected $fillable = [
        'booking_id',
        'requested_date',
        'requested_time',
        'status',
        'rejection_reason',
        'new_team_id',
    ];

    protected $casts = [
        'requested_date' => 'date',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function newTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'new_team_id');
    }
}
