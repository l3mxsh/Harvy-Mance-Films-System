@forelse($postProductions as $pp)
    @php
        $totalTasks = $pp->tasks->count();
        $completedTasks = $pp->tasks->where('admin_review_status', 'approved')->count();
        $pendingReview = $pp->tasks->where('admin_review_status', 'pending')->where('status', 'completed')->count();
        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
    @endphp
    <tr>
        <td><code>{{ $pp->booking->booking_ref ?? 'N/A' }}</code></td>
        <td>
            <div class="fw-semibold">{{ $pp->booking->client_name ?? 'N/A' }}</div>
            <small class="text-muted">{{ $pp->booking->event_type ?? '' }}</small>
        </td>
        <td>
            <span class="badge bg-light text-dark border">{{ $totalTasks }}</span>
            @if($pendingReview > 0)
                <span class="badge bg-warning text-dark">{{ $pendingReview }} to review</span>
            @endif
        </td>
        <td style="min-width: 130px;">
            <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-success" style="width: {{ $progress }}%"></div>
            </div>
            <small class="text-muted">{{ $completedTasks }}/{{ $totalTasks }}</small>
        </td>
        <td>
            @php
                $statusMap = [
                    'in_progress' => ['bg-warning text-dark', 'bi-arrow-repeat', 'In Progress'],
                    'ready' => ['bg-success', 'bi-check2-all', 'Ready for Delivery'],
                    'delivered' => ['bg-dark', 'bi-box-seam', 'Delivered'],
                ];
                [$cls, $icon, $label] = $statusMap[$pp->status] ?? ['bg-secondary', 'bi-circle', ucfirst($pp->status)];
            @endphp
            <span class="badge {{ $cls }}"><i class="bi {{ $icon }} me-1"></i>{{ $label }}</span>
        </td>
        <td>{{ $pp->expected_completion_date ? $pp->expected_completion_date->format('M d, Y') : '—' }}</td>
        <td class="text-center">
            <div class="d-flex justify-content-center gap-2">
                <a href="{{ route('post-production.show', $pp->id) }}" class="btn btn-sm btn-outline-dark rounded-3">View</a>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-4 text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            No post-production projects found.
        </td>
    </tr>
@endforelse
