@forelse($addons as $addon)
    <tr>
        <td class="fw-semibold">{{ $addon->name }}</td>
        <td>
            <small class="text-muted">{{ Str::limit($addon->description, 60) }}</small>
        </td>
        <td class="text-center">
            @if($addon->inventory->count() > 0)
                <span class="badge bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-tools me-1"></i>{{ $addon->inventory->count() }} items
                </span>
            @else
                <span class="text-muted small">None</span>
            @endif
        </td>
        <td class="text-end fw-semibold">&#8369;{{ number_format($addon->price, 2) }}</td>
        <td class="text-center">
            @include('partials.status-badge', ['status' => $addon->status])
        </td>
        <td class="text-center">
            <div class="d-flex justify-content-center gap-2">
                <a class="btn btn-sm btn-outline-secondary rounded-3" title="View & Edit" href="{{ route('addon.edit', $addon->id) }}">
                    <i class="bi bi-eye"></i>
                </a>
                <button class="btn btn-sm btn-outline-danger rounded-3" title="Delete" onclick="confirmDeleteAddon({{ $addon->id }}, '{{ addslashes($addon->name) }}')">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center py-5 text-muted">
            <i class="bi bi-plus-circle fs-1 d-block mb-2"></i>
            No add-ons found. Click "Add Add-On" to create one.
        </td>
    </tr>
@endforelse
