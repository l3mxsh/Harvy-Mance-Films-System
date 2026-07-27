<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostProduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'assigned_staff_id',
        'status',
        'notes',
        'progress_notes',
        'expected_completion_date',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'expected_completion_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'assigned_staff_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(PostProductionTask::class);
    }
}
