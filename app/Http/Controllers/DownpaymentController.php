<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Downpayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DownpaymentController extends Controller
{
    public function showForm()
    {
        $account = auth('customer')->user();
        $booking = Booking::with(['package', 'addons', 'downpayments'])
            ->findOrFail($account->booking_id);

        $latestDownpayment = $booking->latestDownpayment;

        return view('customer.downpayment', compact('account', 'booking', 'latestDownpayment'));
    }

    public function submit(Request $request)
    {
        $account = auth('customer')->user();
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

        return redirect()->route('customer.dashboard')
            ->with('success', 'Payment proof submitted successfully! Awaiting admin verification.');
    }

    public function resubmit(Request $request, Downpayment $downpayment)
    {
        $account = auth('customer')->user();
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

        if ($downpayment->payment_proof && Storage::disk('public')->exists($downpayment->payment_proof)) {
            Storage::disk('public')->delete($downpayment->payment_proof);
        }

        $path = $request->file('payment_proof')->store('downpayment-proofs', 'public');

        $downpayment->update([
            'amount' => $validated['amount'],
            'payment_proof' => $path,
            'status' => 'pending',
            'rejection_reason' => null,
            'submitted_at' => now(),
            'verified_at' => null,
        ]);

        $booking->update(['payment_status' => 'payment_submitted']);

        return redirect()->route('customer.dashboard')
            ->with('success', 'Payment proof resubmitted successfully! Awaiting admin verification.');
    }

    public function showFinalPaymentForm()
    {
        $account = auth('customer')->user();
        $booking = Booking::with(['package', 'addons', 'downpayments'])
            ->findOrFail($account->booking_id);

        $remainingBalance = $booking->total_price - $booking->downpayments()->where('status', 'verified')->sum('amount');
        $latestFinalPayment = $booking->downpayments()->where('payment_type', 'final')->latest()->first();

        return view('customer.final-payment', compact('account', 'booking', 'remainingBalance', 'latestFinalPayment'));
    }

    public function submitFinalPayment(Request $request)
    {
        $account = auth('customer')->user();
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

        return redirect()->route('customer.dashboard')
            ->with('success', 'Final payment proof submitted successfully! Awaiting admin verification.');
    }

    public function resubmitFinalPayment(Request $request, Downpayment $downpayment)
    {
        $account = auth('customer')->user();
        $booking = Booking::findOrFail($account->booking_id);

        if ($downpayment->booking_id !== $booking->id || $downpayment->status !== 'rejected' || $downpayment->payment_type !== 'final') {
            return back()->with('error', 'Invalid payment or cannot be resubmitted.');
        }

        $validated = $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'amount' => 'required|numeric|min:0.01',
        ]);

        if ($downpayment->payment_proof && Storage::disk('public')->exists($downpayment->payment_proof)) {
            Storage::disk('public')->delete($downpayment->payment_proof);
        }

        $path = $request->file('payment_proof')->store('final-payment-proofs', 'public');

        $downpayment->update([
            'amount' => $validated['amount'],
            'payment_proof' => $path,
            'status' => 'pending',
            'rejection_reason' => null,
            'submitted_at' => now(),
            'verified_at' => null,
        ]);

        return redirect()->route('customer.dashboard')
            ->with('success', 'Final payment resubmitted successfully! Awaiting admin verification.');
    }

    public function adminIndex()
    {
        $downpayments = Downpayment::with(['booking.package'])
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

    public function adminShow(Downpayment $downpayment)
    {
        $downpayment->load(['booking.package', 'booking.addons', 'booking.team.members']);

        return view('dashboard.payment-details', compact('downpayment'));
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

        return back()->with('success', 'Payment has been rejected.');
    }
}
