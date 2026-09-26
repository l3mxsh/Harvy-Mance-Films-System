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
     * Human readable labels for the stored action keys.
     */
    protected static array $actionLabels = [
        'auth.login' => 'Signed In',
        'auth.logout' => 'Signed Out',
        'user.created' => 'Admin Added',
        'user.updated' => 'Admin Updated',
        'user.status_changed' => 'Admin Status Changed',
        'user.deleted' => 'Admin Deleted',
        'settings.updated' => 'Settings Updated',
        'admin.profile_updated' => 'Profile Updated',
        'admin.password_changed' => 'Password Changed',
        'booking.approved' => 'Booking Approved',
        'booking.rejected' => 'Booking Rejected',
        'cancellation.refunded' => 'Cancellation Refunded',
        'cancellation.rejected' => 'Cancellation Rejected',
        'payment.verified' => 'Payment Verified',
        'payment.rejected' => 'Payment Rejected',
        'client.restored' => 'Client Restored',
        'client.archived' => 'Client Archived',
        'client.deleted' => 'Client Deleted',
    ];

    /**
     * Turn a stored action key such as "auth.login" into a readable label.
     */
    public static function labelFor(?string $action): string
    {
        $action = (string) $action;

        if (isset(static::$actionLabels[$action])) {
            return static::$actionLabels[$action];
        }

        return ucwords(str_replace(['.', '_'], ' ', $action));
    }

    public function getFriendlyActionAttribute(): string
    {
        return static::labelFor($this->action);
    }

    /**
     * Display the entry owner as "Client: Kyla" instead of separate
     * actor and user type fields.
     */
    public function getActorLabelAttribute(): string
    {
        return ucfirst($this->user_type ?? 'system') . ': ' . ($this->actor_name ?? 'System');
    }

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
