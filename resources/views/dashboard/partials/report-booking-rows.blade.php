{{-- Bookings tab: desktop table rows --}}
@forelse($bookings as $booking)
    @php $balance = max(0, (float) $booking->total_price - (float) $booking->paid_amount); @endphp
    <tr>
        <td><code>{{ $booking->booking_ref }}</code></td>
        <td class="fw-semibold">{{ $booking->client_name }}</td>
        <td class="text-nowrap">{{ $booking->event_date->format('M d, Y') }}</td>
        <td>{{ $booking->package->name ?? '—' }}</td>
        <td>@include('partials.status-badge', ['status' => $booking->status])</td>
        <td class="text-end">&#8369;{{ number_format($booking->total_price, 2) }}</td>
        <td class="text-end text-success">&#8369;{{ number_format($booking->paid_amount, 2) }}</td>
        <td class="text-end {{ $balance > 0 ? 'text-danger' : '' }}">&#8369;{{ number_format($balance, 2) }}</td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center py-4 text-muted">No bookings in this date range.</td>
    </tr>
@endforelse
