<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OutsourcedStaff extends Model
{
    protected $fillable = ['name', 'email', 'contact_number', 'notes'];

    public function tasks(): HasMany
    {
        return $this->hasMany(PostProductionTask::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'outsourced_staff_team')
            ->withTimestamps();
    }
}
