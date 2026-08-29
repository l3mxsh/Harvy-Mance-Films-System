@forelse($packages as $pkg)
    <tr>
        <td>
            <div class="fw-semibold">{{ $pkg->name }}</div>
            @if($pkg->description)
                <small class="text-muted">{{ Str::limit($pkg->description, 50) }}</small>
            @endif
        </td>
        <td>
            @foreach($pkg->services->take(3) as $service)
                <span class="badge bg-light text-dark border me-1">{{ $service->service_name }}</span>
            @endforeach
            @if($pkg->services->count() > 3)
                <span class="badge bg-secondary">+{{ $pkg->services->count() - 3 }} more</span>
            @endif
        </td>
        <td class="text-center">
            @if($pkg->inventory->count() > 0)
                <span class="badge bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-tools me-1"></i>{{ $pkg->inventory->count() }} items
                </span>
            @else
                <span class="text-muted small">None</span>
            @endif
        </td>
        <td class="text-end fw-semibold">&#8369;{{ number_format($pkg->price, 2) }}</td>
        <td class="text-center">
            @include('partials.status-badge', ['status' => $pkg->status])
        </td>
        <td class="text-center">
            <div class="d-flex justify-content-center gap-2">
                <a class="btn btn-sm btn-outline-secondary rounded-3" title="Edit" href="{{ route('package.edit', $pkg->id) }}">
                    <i class="bi bi-pencil"></i>
                </a>
                <button class="btn btn-sm btn-outline-danger rounded-3" title="Delete" onclick="confirmDeletePackage({{ $pkg->id }}, '{{ addslashes($pkg->name) }}')">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center py-5 text-muted">
            <i class="bi bi-box-seam fs-1 d-block mb-2"></i>
            No packages found. Click "Add Package" to create one.
        </td>
    </tr>
@endforelse
