@forelse($addons as $addon)
    <div class="pkg-mobile-card">
        <div class="d-flex justify-content-between align-items-start mb-1">
            <div class="fw-semibold">{{ $addon->name }}</div>
            @include('partials.status-badge', ['status' => $addon->status])
        </div>
        <div class="pkg-mobile-price mb-2">&#8369;{{ number_format($addon->price, 2) }}</div>
        <div class="d-flex gap-2">
            <a class="btn btn-sm btn-outline-secondary rounded-3 flex-fill" href="{{ route('addon.edit', $addon->id) }}">
                <i class="bi bi-eye me-1"></i> View
            </a>
            <button class="btn btn-sm btn-outline-danger rounded-3" onclick="confirmDeleteAddon({{ $addon->id }}, '{{ addslashes($addon->name) }}')">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>
@empty
    <div class="text-center py-5 text-muted">
        <i class="bi bi-plus-circle fs-1 d-block mb-2"></i>
        No add-ons found. Click "Add Add-On" to create one.
    </div>
@endforelse
