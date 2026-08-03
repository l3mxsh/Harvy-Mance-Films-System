@forelse($logs as $log)
    <tr>
        <td>
            <span class="log-actor">
                <span class="log-actor-dot dot-{{ $log->user_type ?? 'system' }}"></span>
                {{ $log->actor_name ?? 'System' }}
            </span>
            <span class="log-user-type">{{ ucfirst($log->user_type ?? 'system') }}</span>
        </td>
        <td>
            <span class="badge bg-light text-dark border">{{ $log->action }}</span>
        </td>
        <td class="log-desc">{{ \Illuminate\Support\Str::limit($log->description, 95) }}</td>
        <td class="text-nowrap">
            {{ $log->created_at->format('M d, Y') }}
            <small class="text-muted d-block">{{ $log->created_at->format('h:i A') }}</small>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-3" title="View details"
                onclick="openLogModal(this)"
                data-id="{{ $log->id }}"
                data-actor="{{ $log->actor_name ?? 'System' }}"
                data-type="{{ $log->user_type ?? 'system' }}"
                data-action="{{ $log->action }}"
                data-time="{{ $log->created_at->format('M d, Y · h:i A') }}"
                data-description="{{ $log->description }}"
                data-context="{{ json_encode($log->context) }}">
                <i class="bi bi-eye"></i>
            </button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center py-4 text-muted">
            <i class="bi bi-activity fs-1 d-block mb-2"></i>
            No activity logs found.
        </td>
    </tr>
@endforelse
