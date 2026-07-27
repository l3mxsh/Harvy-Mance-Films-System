<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostProductionTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_production_id',
        'staff_id',
        'task_type',
        'instructions',
        'status',
        'deliverable_link',
        'remarks',
        'revision_notes',
        'admin_review_status',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function postProduction(): BelongsTo
    {
        return $this->belongsTo(PostProduction::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
