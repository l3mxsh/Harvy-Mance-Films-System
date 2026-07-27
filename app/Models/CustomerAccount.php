<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class CustomerAccount extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'control_number',
        'password',
        'client_name',
        'client_email',
        'client_phone',
        'booking_id',
        'must_change_password',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'must_change_password' => 'boolean',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
