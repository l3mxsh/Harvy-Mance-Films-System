<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Downpayment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DownpaymentController extends Controller
{
    public function submit(Request $request)
    {
        $account = auth('client')->user();
        $booking = Booking::findOrFail($account->booking_id);

        $validated = $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $path = $request->file('payment_proof')->store('downpayment-proofs', 'public');

        $downpayment = Downpayment::create([
            'booking_id' => $booking->id,
            'payment_type' => 'downpayment',
            'amount' => $validated['amount'],
            'payment_proof' => $path,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        $booking->update(['payment_status' => 'payment_submitted']);

        return redirect()->route('client.dashboard')
            ->with('success', 'Payment proof submitted successfully! Awaiting admin verification.');
    }

    public function resubmit(Request $request, Downpayment $downpayment)
    {
        $account = auth('client')->user();
        $booking = Booking::findOrFail($account->booking_id);

        if ($downpayment->booking_id !== $booking->id) {
            abort(403);
        }

        if ($downpayment->status !== 'rejected') {
            return back()->with('error', 'Only rejected payments can be resubmitted.');
        }

        $validated = $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $path = $request->file('payment_proof')->store('downpayment-proofs', 'public');

        Downpayment::create([
            'booking_id' => $booking->id,
            'payment_type' => 'downpayment',
            'amount' => $validated['amount'],
            'payment_proof' => $path,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        $booking->update(['payment_status' => 'payment_submitted']);

        return redirect()->route('client.dashboard')
            ->with('success', 'Payment proof resubmitted successfully! Awaiting admin verification.');
    }

    public function submitFinalPayment(Request $request)
    {
        $account = auth('client')->user();
        $booking = Booking::findOrFail($account->booking_id);

        if (!in_array($booking->status, ['completed', 'ongoing'])) {
            return back()->with('error', 'Final payment is only available after event completion.');
        }

        $validated = $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $path = $request->file('payment_proof')->store('final-payment-proofs', 'public');

        Downpayment::create([
            'booking_id' => $booking->id,
            'payment_type' => 'final',
            'amount' => $validated['amount'],
            'payment_proof' => $path,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        return redirect()->route('client.dashboard')
            ->with('success', 'Final payment proof submitted successfully! Awaiting admin verification.');
    }

    public function resubmitFinalPayment(Request $request, Downpayment $downpayment)
    {
        $account = auth('client')->user();
        $booking = Booking::findOrFail($account->booking_id);

        if ($downpayment->booking_id !== $booking->id || $downpayment->status !== 'rejected' || $downpayment->payment_type !== 'final') {
            return back()->with('error', 'Invalid payment or cannot be resubmitted.');
        }

        $validated = $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $path = $request->file('payment_proof')->store('final-payment-proofs', 'public');

        Downpayment::create([
            'booking_id' => $booking->id,
            'payment_type' => 'final',
            'amount' => $validated['amount'],
            'payment_proof' => $path,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        return redirect()->route('client.dashboard')
            ->with('success', 'Final payment resubmitted successfully! Awaiting admin verification.');
    }

    public function adminIndex()
    {
        $downpayments = Downpayment::query()
            ->with(['booking.package.services', 'booking.addons', 'booking.team'])
            ->when(request('status'), fn ($query, $status) => $query->where('status', $status))
            ->orderBy('submitted_at', 'desc')
            ->paginate(15);

        $stats = [
            'total' => Downpayment::count(),
            'downpayment_total' => Downpayment::where('payment_type', 'downpayment')->count(),
            'final_total' => Downpayment::where('payment_type', 'final')->count(),
            'pending' => Downpayment::where('status', 'pending')->count(),
            'verified' => Downpayment::where('status', 'verified')->count(),
            'rejected' => Downpayment::where('status', 'rejected')->count(),
        ];

        return view('dashboard.payment-verification', compact('downpayments', 'stats'));
    }

    public function adminVerify(Downpayment $downpayment)
    {
        $downpayment->update([
            'status' => 'verified',
            'verified_at' => now(),
        ]);

        $booking = $downpayment->booking;

        $totalPaid = $booking->downpayments()->where('status', 'verified')->sum('amount');
        $updateData = [];

        if ($totalPaid >= $booking->total_price) {
            $updateData['final_payment_status'] = 'paid';
        }

        if ($booking->status === 'approved') {
            $updateData['payment_status'] = 'downpayment_verified';
            $updateData['status'] = 'ongoing';
        }

        if (!empty($updateData)) {
            $booking->update($updateData);
        }

        ActivityLog::log('payment.verified', "Verified {$downpayment->payment_type} payment of ₱{$downpayment->amount} for booking {$booking->booking_ref}.", [
            'booking_ref' => $booking->booking_ref,
            'payment_type' => $downpayment->payment_type,
            'amount' => $downpayment->amount,
        ]);

        return back()->with('success', 'Payment has been verified successfully.');
    }

    public function adminReject(Request $request, Downpayment $downpayment)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $downpayment->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        $downpayment->booking->update(['payment_status' => 'payment_rejected']);

        ActivityLog::log('payment.rejected', "Rejected {$downpayment->payment_type} payment of ₱{$downpayment->amount} for booking {$downpayment->booking->booking_ref}.", [
            'booking_ref' => $downpayment->booking->booking_ref,
            'payment_type' => $downpayment->payment_type,
            'reason' => $validated['rejection_reason'],
        ]);

        return back()->with('success', 'Payment has been rejected.');
    }
}
