<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Staff extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'contact_number',
        'status',
        'last_login_at',
        'is_outsourced',
        'is_temporary',
        'temp_expires_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'last_login_at' => 'datetime',
            'temp_expires_at' => 'datetime',
            'is_outsourced' => 'boolean',
            'is_temporary' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function isExpiredTemp(): bool
    {
        return $this->is_temporary && $this->temp_expires_at && now()->gt($this->temp_expires_at);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'staff_team')
            ->withTimestamps();
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(StaffSchedule::class);
    }

    public function postProductionTasks(): HasMany
    {
        return $this->hasMany(PostProductionTask::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isAvailableOn(string $date): bool
    {
        return !$this->schedules()
            ->where('event_date', $date)
            ->whereIn('status', ['assigned', 'confirmed'])
            ->exists();
    }
}
