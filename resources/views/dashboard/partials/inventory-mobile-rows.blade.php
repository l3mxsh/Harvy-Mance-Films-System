@forelse($items as $item)
    <div class="pkg-mobile-card">
        <div class="d-flex justify-content-between align-items-start mb-1">
            <div>
                <div class="fw-semibold">{{ $item->name }}</div>
            </div>
            @if($item->category === 'equipment')
                <span class="badge bg-primary bg-opacity-10 text-primary flex-shrink-0 ms-2">Equipment</span>
            @else
                <span class="badge bg-purple text-white flex-shrink-0 ms-2">Material</span>
            @endif
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
            <span class="small text-muted">Qty: <span class="fw-semibold text-dark {{ $item->quantity <= 2 ? 'text-danger' : '' }}">{{ $item->quantity }}</span> {{ $item->unit }}</span>
            <span class="mx-1 text-muted">·</span>
            @include('partials.status-badge', ['status' => $item->condition_status])
            @include('partials.status-badge', ['status' => $item->availability_status])
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary rounded-3 flex-fill" title="View" onclick="viewItem({{ $item->id }})">
                <i class="bi bi-eye me-1"></i> View
            </button>
            <button class="btn btn-sm btn-outline-dark rounded-3 flex-fill" title="Edit" onclick="editItem({{ $item->id }})">
                <i class="bi bi-pencil me-1"></i> Edit
            </button>
            <button class="btn btn-sm btn-outline-danger rounded-3" title="Delete" onclick="confirmDeleteItem({{ $item->id }}, '{{ addslashes($item->name) }}')">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>
@empty
    <div class="text-center py-5 text-muted">
        <i class="bi bi-box-seam fs-1 d-block mb-2"></i>
        No inventory items found. Click "Add Item" to create one.
    </div>
@endforelse
