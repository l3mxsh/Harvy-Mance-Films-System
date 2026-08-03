<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_type',
        'actor_name',
        'action',
        'description',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    /**
     * Record an activity entry using the currently authenticated actor
     * (admin web guard, staff guard, or client guard).
     */
    public static function log(string $action, string $description, array $context = []): self
    {
        $userId = null;
        $userType = null;
        $actorName = null;

        if ($user = auth()->user()) {
            $userType = 'admin';
            $userId = $user->id;
            $actorName = $user->name;
        } elseif ($client = auth('client')->user()) {
            $userType = 'client';
            $userId = $client->id;
            $actorName = $client->client_name ?? $client->name ?? 'Client';
        } elseif ($staff = auth('staff')->user()) {
            $userType = 'staff';
            $userId = $staff->id;
            $actorName = $staff->name ?? 'Staff';
        }

        return self::create([
            'user_id'     => $userId,
            'user_type'   => $userType,
            'actor_name'  => $actorName,
            'action'      => $action,
            'description' => $description,
            'context'     => $context,
        ]);
    }
}
