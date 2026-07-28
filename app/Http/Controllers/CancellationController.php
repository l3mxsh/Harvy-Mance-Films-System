<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\CancellationRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CancellationController extends Controller
{
    /**
     * Compute refund percentage based on days until event and admin-configured policy.
     * Policy stored as JSON: [{"days":14,"percent":100},{"days":7,"percent":50},{"days":0,"percent":0}]
     * Sorted descending by days; first matching tier wins.
     */
    public static function computeRefundPercentage(Booking $booking): int
    {
        $daysUntil = (int) now()->startOfDay()->diffInDays($booking->event_date->startOfDay(), false);
        $policy = json_decode(Setting::getValue('refund_policy', json_encode([
            ['days' => 14, 'percent' => 100],
            ['days' => 7,  'percent' => 50],
            ['days' => 0,  'percent' => 0],
        ])), true);

        usort($policy, fn($a, $b) => $b['days'] <=> $a['days']);

        foreach ($policy as $tier) {
            if ($daysUntil >= $tier['days']) {
                return (int) $tier['percent'];
            }
        }

        return 0;
    }

    /** Customer: submit cancellation request */
    public function store(Request $request)
    {
        $account = Auth::guard('customer')->user();
        $booking = $account->booking;

        if (!in_array($booking->status, ['pending', 'approved', 'ongoing'])) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        if ($booking->cancellationRequest && in_array($booking->cancellationRequest->status, ['pending', 'approved'])) {
            return back()->with('error', 'A cancellation request is already submitted.');
        }

        $request->validate(['reason' => 'nullable|string|max:1000']);

        $amountPaid = $booking->downpayments()->where('status', 'verified')->sum('amount');
        $refundPercent = self::computeRefundPercentage($booking);
        $refundAmount = round($amountPaid * ($refundPercent / 100), 2);

        // No payment made — cancel immediately, no refund review needed
        if ($amountPaid <= 0) {
            CancellationRequest::create([
                'booking_id'        => $booking->id,
                'reason'            => $request->reason,
                'refund_percentage' => 0,
                'refund_amount'     => 0,
                'status'            => 'refunded',
                'processed_at'      => now(),
            ]);
            $booking->update(['status' => 'cancelled']);
            $booking->items()->update(['status' => 'cancelled']);
            $booking->staffSchedules()->delete();
            return back()->with('success', 'Booking cancelled successfully.');
        }

        CancellationRequest::create([
            'booking_id'        => $booking->id,
            'reason'            => $request->reason,
            'refund_percentage' => $refundPercent,
            'refund_amount'     => $refundAmount,
            'status'            => 'pending',
        ]);

        return back()->with('success', $refundAmount > 0
            ? "Cancellation request submitted. A refund of ₱" . number_format($refundAmount, 2) . " ({$refundPercent}%) is pending admin review."
            : 'Cancellation request submitted. No refund is applicable based on the current policy. Awaiting admin confirmation.');
    }

    /** Admin: list all cancellation requests */
    public function adminIndex()
    {
        $cancellations = CancellationRequest::with(['booking.downpayments'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('dashboard.cancellations', compact('cancellations'));
    }

    /** Admin: approve refund and mark as refunded */
    public function approve(Request $request, CancellationRequest $cancellation)
    {
        $request->validate([
            'refund_reference' => 'nullable|string|max:255',
            'refund_proof'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'admin_notes'      => 'nullable|string|max:1000',
        ]);

        $proofPath = null;
        if ($request->hasFile('refund_proof')) {
            $proofPath = $request->file('refund_proof')->store('refund_proofs', 'public');
        }

        $cancellation->update([
            'status'           => 'refunded',
            'refund_reference' => $request->refund_reference,
            'refund_proof'     => $proofPath,
            'admin_notes'      => $request->admin_notes,
            'processed_at'     => now(),
        ]);

        $cancellation->booking->update(['status' => 'cancelled']);
        $cancellation->booking->items()->update(['status' => 'cancelled']);
        $cancellation->booking->staffSchedules()->delete();

        return back()->with('success', "Refund marked as processed for booking {$cancellation->booking->booking_ref}.");
    }

    /** Admin: reject refund request */
    public function reject(Request $request, CancellationRequest $cancellation)
    {
        $request->validate(['admin_notes' => 'required|string|max:1000']);

        $cancellation->update([
            'status'       => 'rejected',
            'admin_notes'  => $request->admin_notes,
            'processed_at' => now(),
        ]);

        return back()->with('success', "Refund request rejected for booking {$cancellation->booking->booking_ref}. Booking remains active.");
    }
}
