<?php

/**
 * Delete Customer Script - FOR TESTING ONLY
 * Usage: php delete_customer.php <control_number|email|booking_ref>
 *        php delete_customer.php --all
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\CustomerAccount;
use App\Models\Booking;
use App\Models\Downpayment;
use App\Models\StaffSchedule;
use App\Models\RescheduleRequest;
use App\Models\CancellationRequest;
use App\Models\PostProductionTask;
use App\Models\PostProduction;
use App\Models\BookingItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

$identifier = $argv[1] ?? null;

if (!$identifier) {
    echo "Usage: php delete_customer.php <control_number|email|booking_ref>\n";
    echo "       php delete_customer.php --all\n";
    exit(1);
}

// ─── DELETE ALL ────────────────────────────────────────────────────────────────
if ($identifier === '--all') {
    $accounts  = CustomerAccount::with('booking')->get();
    $bookings  = Booking::all();
    $payments  = Downpayment::all();
    $schedules = StaffSchedule::count();
    $ppRecords = PostProduction::count();
    $ppTasks   = PostProductionTask::count();

    echo "===========================================\n";
    echo "Customer accounts  : {$accounts->count()}\n";
    echo "Bookings           : {$bookings->count()}\n";
    echo "Payment records    : {$payments->count()}\n";
    echo "Staff schedules    : {$schedules}\n";
    echo "Post-production    : {$ppRecords} record(s), {$ppTasks} task(s)\n";
    echo "===========================================\n";

    if ($accounts->isNotEmpty()) {
        echo "Accounts to delete:\n";
        foreach ($accounts as $acc) {
            echo "  - {$acc->client_name} ({$acc->control_number})";
            if ($acc->booking) echo " | {$acc->booking->booking_ref} | {$acc->booking->status}";
            echo "\n";
        }
    }

    echo "===========================================\n";
    echo "WARNING: This will permanently delete EVERYTHING.\n";
    echo "Type 'yes' to confirm: ";

    if (trim(fgets(STDIN)) !== 'yes') {
        echo "Cancelled.\n";
        exit(0);
    }

    DB::transaction(function () use ($accounts) {
        // 1. Delete via accounts (handles linked bookings properly)
        foreach ($accounts as $acc) {
            deleteAccount($acc);
        }

        // 2. Clean up orphaned bookings (no customer account)
        $orphanedBookings = Booking::doesntHave('customerAccount')->get();
        foreach ($orphanedBookings as $booking) {
            deleteBookingData($booking);
            $booking->delete();
        }

        // 3. Clean up orphaned payment records (booking_id points to non-existent booking)
        $orphanedPayments = Downpayment::whereNotIn('booking_id', Booking::pluck('id'))->get();
        foreach ($orphanedPayments as $dp) {
            if ($dp->payment_proof && Storage::disk('public')->exists($dp->payment_proof)) {
                Storage::disk('public')->delete($dp->payment_proof);
            }
            $dp->delete();
        }

        // 4. Clean up orphaned post-production records (booking_id points to non-existent booking)
        $orphanedPP = PostProduction::whereNotIn('booking_id', Booking::pluck('id'))->get();
        foreach ($orphanedPP as $pp) {
            PostProductionTask::where('post_production_id', $pp->id)->delete();
            $pp->delete();
        }

        // 5. Clean up any remaining orphaned records
        CancellationRequest::whereDoesntHave('booking')->delete();
        RescheduleRequest::whereDoesntHave('booking')->delete();
        StaffSchedule::whereDoesntHave('booking')->delete();
        BookingItem::whereDoesntHave('booking')->delete();
    });

    echo "Done. All records deleted.\n";
    exit(0);
}

// ─── DELETE SINGLE ─────────────────────────────────────────────────────────────
$account = CustomerAccount::where('control_number', $identifier)
    ->orWhere('client_email', $identifier)
    ->first();

if (!$account) {
    $account = CustomerAccount::whereHas('booking', fn($q) => $q->where('booking_ref', $identifier))->first();
}

if (!$account) {
    echo "ERROR: No customer account found for '{$identifier}'\n";
    exit(1);
}

$booking = $account->booking;

echo "===========================================\n";
echo "Account   : {$account->client_name}\n";
echo "Control # : {$account->control_number}\n";
echo "Email     : {$account->client_email}\n";
if ($booking) {
    echo "Booking   : {$booking->booking_ref}\n";
    echo "Status    : {$booking->status}\n";
    echo "Event Date: {$booking->event_date->format('M d, Y')}\n";
    echo "Payments  : " . $booking->downpayments()->count() . " record(s)\n";
    echo "Reschedule: " . $booking->rescheduleRequests()->count() . " request(s)\n";
    echo "Cancellation: " . CancellationRequest::where('booking_id', $booking->id)->count() . " request(s)\n";
}
echo "===========================================\n";
echo "WARNING: This will permanently delete the account and ALL related data.\n";
echo "Type 'yes' to confirm: ";

if (trim(fgets(STDIN)) !== 'yes') {
    echo "Cancelled.\n";
    exit(0);
}

DB::transaction(fn() => deleteAccount($account));
echo "Done. All records deleted successfully.\n";

// ─── HELPERS ───────────────────────────────────────────────────────────────────

function deleteAccount(CustomerAccount $account): void
{
    $booking = $account->booking;
    if ($booking) {
        deleteBookingData($booking);
        $booking->delete();
    }
    $account->delete();
}

function deleteBookingData(Booking $booking): void
{
    // Post-production tasks + record
    if ($booking->postProduction) {
        PostProductionTask::where('post_production_id', $booking->postProduction->id)->delete();
        $booking->postProduction->delete();
    }

    // Downpayments + final payments (payment verification) + proof files
    foreach (Downpayment::where('booking_id', $booking->id)->get() as $dp) {
        if ($dp->payment_proof && Storage::disk('public')->exists($dp->payment_proof)) {
            Storage::disk('public')->delete($dp->payment_proof);
        }
        $dp->delete();
    }

    // Cancellation requests + refund proof files
    foreach (CancellationRequest::where('booking_id', $booking->id)->get() as $c) {
        if ($c->refund_proof && Storage::disk('public')->exists($c->refund_proof)) {
            Storage::disk('public')->delete($c->refund_proof);
        }
        $c->delete();
    }

    // Reschedule requests
    RescheduleRequest::where('booking_id', $booking->id)->delete();

    // Staff schedules
    StaffSchedule::where('booking_id', $booking->id)->delete();

    // Booking items + addon pivots
    BookingItem::where('booking_id', $booking->id)->delete();
    $booking->addons()->detach();
}
