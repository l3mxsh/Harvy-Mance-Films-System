@forelse($accounts as $account)
    <tr>
        <td><code>{{ $account->control_number }}</code></td>
        <td>
            <div>{{ $account->client_name }}</div>
            <small class="text-muted">{{ $account->client_email }}</small>
        </td>
        <td>
            @if($account->booking)
                <code>{{ $account->booking->booking_ref }}</code>
            @else
                <span class="text-muted fst-italic">&mdash;</span>
            @endif
        </td>
        <td>{{ $account->client_phone ?? '—' }}</td>
        <td>
            @if($account->last_login_at)
                {{ $account->last_login_at->format('M d, Y g:i A') }}
            @else
                <span class="text-muted fst-italic">Never</span>
            @endif
        </td>
        <td>{{ $account->created_at->format('M d, Y') }}</td>
        <td>
            @if($account->must_change_password)
                <span class="badge bg-warning text-dark">
                    <i class="bi bi-key me-1"></i>First login pending
                </span>
            @else
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>Active
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-dark rounded-pill"
                                            data-bs-toggle="modal"
                                            data-bs-target="#archiveModal"
                                            data-id="{{ $account->id }}"
                                            data-name="{{ $account->client_name }}"
                                            data-control="{{ $account->control_number }}">
                                        <i class="bi bi-archive me-1"></i>Archive
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No client accounts found.
                                </td>
                            </tr>
                        @endforelse
