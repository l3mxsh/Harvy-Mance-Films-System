<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\OutsourcedStaff;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class, 'staff_team')
            ->withTimestamps();
    }

    public function outsourcedMembers(): BelongsToMany
    {
        return $this->belongsToMany(OutsourcedStaff::class, 'outsourced_staff_team')
            ->withTimestamps();
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
