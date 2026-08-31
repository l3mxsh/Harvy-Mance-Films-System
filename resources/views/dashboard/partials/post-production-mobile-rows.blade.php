@forelse($postProductions as $pp)
    @php
        $totalTasks = $pp->tasks->count();
        $completedTasks = $pp->tasks->where('admin_review_status', 'approved')->count();
        $pendingReview = $pp->tasks->where('admin_review_status', 'pending')->where('status', 'completed')->count();
        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
    @endphp
    <div class="mobile-card">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
                <div class="fw-semibold">{{ $pp->booking->client_name ?? 'N/A' }}</div>
                <small class="text-muted"><code>{{ $pp->booking->booking_ref ?? 'N/A' }}</code></small>
            </div>
            <div class="text-end flex-shrink-0 ms-2">
                @include('partials.status-badge', [
                    'status' => $pp->status,
                    'label' => match ($pp->status) {
                        'in_progress' => 'In Progress',
                        'ready' => 'Ready for Delivery',
                        'delivered' => 'Delivered',
                        default => null,
                    },
                ])
            </div>
        </div>
        <div class="d-flex flex-wrap gap-1 mb-2 small">
            <span class="mobile-card-label w-100 mb-0">Event</span>
            <span class="mobile-card-value">{{ $pp->booking->event_type ?? '—' }}</span>
        </div>
        <div class="d-flex flex-wrap gap-1 mb-2 small">
            <span class="mobile-card-label w-100 mb-0">Tasks</span>
            <span class="badge bg-light text-dark border">{{ $totalTasks }}</span>
            @if($pendingReview > 0)
                <span class="badge bg-warning text-dark">{{ $pendingReview }} to review</span>
            @endif
        </div>
        <div class="d-flex flex-wrap gap-1 mb-2 small">
            <span class="mobile-card-label w-100 mb-0">Progress</span>
            <span class="mobile-card-value w-100">
                <span class="progress d-block" style="height: 6px;">
                    <span class="progress-bar bg-success" style="width: {{ $progress }}%"></span>
                </span>
                <small class="text-muted">{{ $completedTasks }}/{{ $totalTasks }} · {{ $progress }}%</small>
            </span>
        </div>
        <div class="d-flex flex-wrap gap-1 mb-2 small">
            <span class="mobile-card-label w-100 mb-0">Due Date</span>
            <span class="mobile-card-value">{{ $pp->expected_completion_date ? $pp->expected_completion_date->format('M d, Y') : '—' }}</span>
        </div>
        <div class="mt-2 d-flex gap-2">
            <a href="{{ route('post-production.show', $pp->id) }}" class="btn btn-sm btn-outline-dark rounded-3 flex-fill">
                <i class="bi bi-eye me-1"></i> View
            </a>
        </div>
    </div>
@empty
    <div class="text-center py-5 text-muted">
        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
        No post-production projects found.
    </div>
@endforelse
