@forelse($items as $item)
    <tr>
        <td>
            <div class="fw-semibold">{{ $item->name }}</div>
            @if($item->description)
                <small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
            @endif
        </td>
        <td class="text-center">
            @if($item->category === 'equipment')
                <span class="badge bg-primary bg-opacity-10 text-primary">Equipment</span>
            @else
                <span class="badge bg-purple text-white">Material</span>
            @endif
        </td>
        <td class="text-center">
            <span class="fw-semibold {{ $item->quantity <= 2 ? 'text-danger' : '' }}">{{ $item->quantity }}</span>
        </td>
        <td>{{ $item->unit }}</td>
        <td class="text-center">
            @include('partials.status-badge', ['status' => $item->condition_status])
        </td>
        <td class="text-center">
            @include('partials.status-badge', ['status' => $item->availability_status])
        </td>
        <td class="text-center">
            <div class="d-flex justify-content-center gap-2">
                <button class="btn btn-sm btn-outline-secondary rounded-3" title="View" onclick="viewItem({{ $item->id }})">
                    <i class="bi bi-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-dark rounded-3" title="Edit" onclick="editItem({{ $item->id }})">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger rounded-3" title="Delete" onclick="confirmDeleteItem({{ $item->id }}, '{{ addslashes($item->name) }}')">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-5 text-muted">
            <i class="bi bi-box-seam fs-1 d-block mb-2"></i>
            No inventory items found. Click "Add Item" to create one.
        </td>
    </tr>
@endforelse
