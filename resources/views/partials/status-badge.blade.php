{{--
    Consistent status badge (label only).
    Usage: @include('partials.status-badge', ['status' => $item->status, 'label' => 'Optional Label', 'id' => 'optional-id'])
    Statuses map to a canonical Bootstrap bg color in $map below.
--}}
@php
    $map = [
        'pending'            => 'bg-warning text-dark',
        'awaiting'           => 'bg-info',
        'awaiting_payment'   => 'bg-info',
        'awaiting_review'    => 'bg-info',
        'submitted'          => 'bg-warning text-dark',
        'not_yet_submitted'  => 'bg-secondary',
        'approved'           => 'bg-success',
        'verified'           => 'bg-success',
        'confirmed'          => 'bg-primary',
        'ongoing'            => 'bg-primary',
        'in_progress'        => 'bg-warning text-dark',
        'completed'          => 'bg-success',
        'delivered'          => 'bg-success',
        'ready'              => 'bg-info',
        'revision_requested' => 'bg-danger',
        'revision'           => 'bg-danger',
        'rejected'           => 'bg-danger',
        'cancelled'          => 'bg-danger',
        'refunded'           => 'bg-success',
        'active'             => 'bg-success',
        'inactive'           => 'bg-secondary',
        'available'          => 'bg-success',
        'in_use'             => 'bg-info',
        'reserved'           => 'bg-warning text-dark',
        'unavailable'        => 'bg-secondary',
        'new'                => 'bg-success',
        'good'               => 'bg-info',
        'maintenance'        => 'bg-warning text-dark',
        'damaged'            => 'bg-danger',
        'paid'               => 'bg-success',
        'fully_paid'         => 'bg-success',
        'unpaid'             => 'bg-danger',
        'balance_due'        => 'bg-danger',
        'unlocked'           => 'bg-success',
        'locked'             => 'bg-secondary',
        'assigned'           => 'bg-warning text-dark',
    ];
    $key = strtolower(str_replace('-', '_', trim($status ?? '')));
    $color = $map[$key] ?? 'bg-secondary';
    $label = $label ?? ucwords(str_replace('_', ' ', $key));
@endphp
@if(!empty($id))<span id="{{ $id }}" class="badge {{ $color }}">{{ $label }}</span>@else<span class="badge {{ $color }}">{{ $label }}</span>@endif
