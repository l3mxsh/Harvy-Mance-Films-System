@forelse($packages as $pkg)
    <div class="pkg-mobile-card">
        <div class="d-flex justify-content-between align-items-start mb-1">
            <div class="fw-semibold">{{ $pkg->name }}</div>
            @include('partials.status-badge', ['status' => $pkg->status])
        </div>
        <div class="pkg-mobile-price mb-2">&#8369;{{ number_format($pkg->price, 2) }}</div>
        <div class="d-flex gap-2">
            <a class="btn btn-sm btn-outline-secondary rounded-3 flex-fill" href="{{ route('package.edit', $pkg->id) }}">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <button class="btn btn-sm btn-outline-danger rounded-3" onclick="confirmDeletePackage({{ $pkg->id }}, '{{ addslashes($pkg->name) }}')">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>
@empty
    <div class="text-center py-5 text-muted">
        <i class="bi bi-box-seam fs-1 d-block mb-2"></i>
        No packages found. Click "Add Package" to create one.
    </div>
@endforelse
