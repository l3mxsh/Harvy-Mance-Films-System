{{-- INVOICE MODAL --}}
@php
    $businessName = 'HarvyMance Films';
    $businessTagline = 'Professional Film & Photography Services';
    $businessAddress = '123 Mabini Street, Brgy. San Roque, Quezon City, Metro Manila';
    $businessPhone = '+63 917 123 4567';
    $businessEmail = 'hello@harvymancefilms.com';

    $invoiceNumber = 'INV-' . $booking->booking_ref;
    $invoiceDate = $booking->created_at ? $booking->created_at->format('M d, Y') : now()->format('M d, Y');
    $invoiceClientName = $account->client_name ?: $booking->client_name;
    $invoiceClientEmail = $account->client_email ?: $booking->client_email;
    $invoiceClientPhone = $account->client_phone ?: $booking->client_phone;

    $invoicePackage = $booking->package;
    $invoiceAddons = $booking->addons;
    $invoicePackagePrice = (float) ($invoicePackage->price ?? 0);
    $invoiceSubtotal = $invoicePackagePrice + (float) $invoiceAddons->sum('pivot.price');
    $invoiceDiscount = 0.0;
    $invoiceTotal = (float) $booking->total_price;

    $invoiceTotalPaid = (float) $booking->downpayments()->where('status', 'verified')->sum('amount');
    $invoiceDownpayment = (float) $booking->downpayment_amount;
    $invoiceRemaining = max(0, $invoiceTotal - $invoiceTotalPaid);

    $invoiceStatusLabel = 'Unpaid';
    $invoiceStatusClass = 'bg-secondary';
    if ($invoiceTotal > 0 && $invoiceTotalPaid >= $invoiceTotal) {
        $invoiceStatusLabel = 'Paid';
        $invoiceStatusClass = 'bg-success';
    } elseif ($invoiceTotalPaid > 0) {
        $invoiceStatusLabel = 'Partially Paid';
        $invoiceStatusClass = 'bg-warning';
    } elseif (in_array($booking->status, ['pending', 'approved', 'ongoing', 'completed'])) {
        $invoiceStatusLabel = 'Pending';
        $invoiceStatusClass = 'bg-warning';
    }

    $invoiceEventDate = $booking->event_date ? $booking->event_date->format('l, F d, Y') : '—';
    $invoiceEventTime = $booking->event_time ? date('g:i A', strtotime($booking->event_time)) : '—';
@endphp

<div class="modal fade" id="invoiceModal" tabindex="-1" aria-hidden="true" aria-labelledby="invoiceModalLabel">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable invoice-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invoiceModalLabel"><i class="bi bi-receipt me-2"></i>Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body invoice-modal-body">

                <div id="invoiceArea" class="invoice-sheet" data-invoice-filename="invoice-{{ $booking->booking_ref }}.pdf">

                    {{-- HEADER --}}
                    <div class="invoice-head">
                        <div class="invoice-brand">
                            <img src="{{ asset('storage/images/Black Logo.png') }}" alt="{{ $businessName }}">
                            <div>
                                <div class="invoice-brand-name">{{ $businessName }}</div>
                                <div class="invoice-brand-sub">{{ $businessTagline }}</div>
                                <div class="invoice-brand-info">
                                    <span>{{ $businessAddress }}</span>
                                    <span>{{ $businessPhone }}</span>
                                    <span>{{ $businessEmail }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-meta">
                            <div class="invoice-title">INVOICE</div>
                            <div class="invoice-meta-row">
                                <span class="meta-key">Invoice No.</span>
                                <strong>{{ $invoiceNumber }}</strong>
                            </div>
                            <div class="invoice-meta-row">
                                <span class="meta-key">Invoice Date</span>
                                <strong>{{ $invoiceDate }}</strong>
                            </div>
                        </div>
                    </div>

                    {{-- BILLED TO / BOOKING DETAILS --}}
                    <div class="invoice-parties">
                        <div>
                            <div class="invoice-label">Billed To</div>
                            <div class="invoice-client-name">{{ $invoiceClientName }}</div>
                            <div class="muted"><i class="bi bi-envelope me-1"></i>{{ $invoiceClientEmail }}</div>
                            <div class="muted"><i class="bi bi-telephone me-1"></i>{{ $invoiceClientPhone }}</div>
                        </div>
                        <div>
                            <div class="invoice-label">Booking Details</div>
                            <div class="invoice-booking-row">
                                <span>Booking Reference</span>
                                <strong>{{ $booking->booking_ref }}</strong>
                            </div>
                            <div class="invoice-booking-row">
                                <span>Event Type</span>
                                <strong>{{ $booking->event_type }}</strong>
                            </div>
                            <div class="invoice-booking-row">
                                <span>Event Date</span>
                                <strong>{{ $invoiceEventDate }}</strong>
                            </div>
                            <div class="invoice-booking-row">
                                <span>Event Time</span>
                                <strong>{{ $invoiceEventTime }}</strong>
                            </div>
                            @if($booking->event_venue)
                                <div class="invoice-booking-row">
                                    <span>Venue</span>
                                    <strong>{{ $booking->event_venue }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ITEMS TABLE --}}
                    <div class="invoice-table-wrap">
                        <table class="invoice-table">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th class="right">Qty</th>
                                    <th class="right">Unit Price</th>
                                    <th class="right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="item-name">{{ $invoicePackage->name ?? 'N/A' }}</div>
                                        @if($invoicePackage && $invoicePackage->services->count() > 0)
                                            <div class="item-desc">{{ $invoicePackage->services->pluck('service_name')->join(', ') }}</div>
                                        @endif
                                    </td>
                                    <td class="right">1</td>
                                    <td class="right">&#8369;{{ number_format($invoicePackagePrice, 2) }}</td>
                                    <td class="right">&#8369;{{ number_format($invoicePackagePrice, 2) }}</td>
                                </tr>
                                @foreach($invoiceAddons as $invoiceAddon)
                                    @php $invoiceAddonPrice = (float) $invoiceAddon->pivot->price; @endphp
                                    <tr>
                                        <td>
                                            <div class="item-name">{{ $invoiceAddon->name }}</div>
                                            @if($invoiceAddon->description)
                                                <div class="item-desc">{{ $invoiceAddon->description }}</div>
                                            @endif
                                        </td>
                                        <td class="right">1</td>
                                        <td class="right">&#8369;{{ number_format($invoiceAddonPrice, 2) }}</td>
                                        <td class="right">&#8369;{{ number_format($invoiceAddonPrice, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- SUMMARY --}}
                    <div class="invoice-summary">
                        <div class="invoice-note">
                            <strong>Thank you for choosing {{ $businessName }}!</strong>
                            <div class="mt-1">This invoice serves as your official payment record.</div>
                        </div>
                        <div class="invoice-totals">
                            @if($invoiceDiscount > 0)
                                <div class="inv-total-row">
                                    <span>Subtotal</span>
                                    <strong>&#8369;{{ number_format($invoiceSubtotal, 2) }}</strong>
                                </div>
                                <div class="inv-total-row">
                                    <span>Discount</span>
                                    <strong>&#8369;{{ number_format($invoiceDiscount, 2) }}</strong>
                                </div>
                            @endif
                            <div class="inv-total-row grand">
                                <span>Total Amount</span>
                                <strong>&#8369;{{ number_format($invoiceTotal, 2) }}</strong>
                            </div>
                            <div class="inv-total-row">
                                <span>Downpayment</span>
                                <strong>&#8369;{{ number_format($invoiceDownpayment, 2) }}</strong>
                            </div>
                            <div class="inv-total-row paid">
                                <span>Amount Paid</span>
                                <strong>&#8369;{{ number_format($invoiceTotalPaid, 2) }}</strong>
                            </div>
                            <div class="inv-total-row balance">
                                <span>Remaining Balance</span>
                                <strong>&#8369;{{ number_format($invoiceRemaining, 2) }}</strong>
                            </div>
                            <div class="invoice-payment-status">
                                <span class="meta-key">Payment Status</span>
                                <span class="badge {{ $invoiceStatusClass }}">{{ $invoiceStatusLabel }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-outline-dark-soft rounded-pill" id="invoicePrintBtn">
                    <i class="bi bi-printer me-1"></i>Print Invoice
                </button>
                <button type="button" class="btn btn-primary-dark rounded-pill" id="invoiceDownloadBtn">
                    <i class="bi bi-download me-1"></i>Download Invoice
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Off-screen container used for PDF capture and printing --}}
<div id="invoicePrintArea" aria-hidden="true"></div>
