<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\RescheduleRequest;
use App\Models\CancellationRequest;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_ref',
        'package_id',
        'team_id',
        'event_type',
        'client_name',
        'client_email',
        'client_phone',
        'event_date',
        'event_time',
        'event_venue',
        'event_address',
        'event_description',
        'total_price',
        'downpayment_amount',
        'status',
        'payment_status',
        'final_payment_status',
        'deliverables_unlocked',
        'rejection_reason',
        'post_production_status',
        'event_completed_at',
        'delivered_at',
        'notes',
        'terms_agreed',
        'reschedule_used',
    ];

    protected $casts = [
        'event_date' => 'date',
        'total_price' => 'decimal:2',
        'downpayment_amount' => 'decimal:2',
        'terms_agreed' => 'boolean',
        'event_completed_at' => 'datetime',
        'delivered_at' => 'datetime',
        'deliverables_unlocked' => 'boolean',
        'reschedule_used' => 'boolean',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function staffSchedules(): HasMany
    {
        return $this->hasMany(StaffSchedule::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function addons(): BelongsToMany
    {
        return $this->belongsToMany(Addon::class, 'booking_addons')
            ->withPivot('price')
            ->withTimestamps();
    }

    public function clientAccount(): HasOne
    {
        return $this->hasOne(ClientAccount::class);
    }

    public function downpayments(): HasMany
    {
        return $this->hasMany(Downpayment::class);
    }

    public function latestDownpayment()
    {
        return $this->hasOne(Downpayment::class)
            ->where('payment_type', 'downpayment')
            ->latestOfMany();
    }

    public function postProduction()
    {
        return $this->hasOne(PostProduction::class);
    }

    public function rescheduleRequests(): HasMany
    {
        return $this->hasMany(RescheduleRequest::class);
    }

    public function pendingReschedule()
    {
        return $this->hasOne(RescheduleRequest::class)->where('status', 'pending')->latestOfMany();
    }

    public function cancellationRequest(): HasOne
    {
        return $this->hasOne(CancellationRequest::class)->latestOfMany();
    }
}
