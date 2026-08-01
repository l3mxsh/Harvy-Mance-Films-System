<?php

/**
 * Simulate Client Account Archiving - FOR TESTING ONLY
 *
 * Usage:
 *   php simulate_archive.php                          # List eligible bookings
 *   php simulate_archive.php <booking_ref|id>         # Simulate archive for one booking (backdates delivery if needed)
 *   php simulate_archive.php --all                    # Simulate archive for ALL eligible bookings
 *   php simulate_archive.php --restore <account_id>   # Restore (un-archive) an archived account
 *   php simulate_archive.php --restore-all            # Un-archive every archived account
 *
 * Optional flags:  --days=N   override the archive window (default: admin setting)
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Booking;
use App\Models\CustomerAccount;
use App\Models\Setting;
use Illuminate\Support\Facades\Artisan;

$args = $argv;
array_shift($args);

$daysOverride = null;
foreach ($args as $i => $arg) {
    if (preg_match('/^--days=(\d+)$/', $arg, $m)) {
        $daysOverride = (int) $m[1];
        unset($args[$i]);
    }
}
$args = array_values($args);

$autoDeleteDays = $daysOverride ?? (int) Setting::getValue('client_auto_delete_days', '30');

// ─── RESTORE MODE ───────────────────────────────────────────────────────────────
if (($args[0] ?? null) === '--restore-all') {
    $archived = CustomerAccount::whereNotNull('archived_at')->get();
    if ($archived->isEmpty()) {
        echo "No archived accounts to restore.\n";
        exit(0);
    }
    foreach ($archived as $acc) {
        $acc->update(['archived_at' => null]);
        echo "Restored: {$acc->client_name} ({$acc->control_number})\n";
    }
    echo "Done. Restored " . $archived->count() . " account(s).\n";
    exit(0);
}

if (($args[0] ?? null) === '--restore') {
    $id = $args[1] ?? null;
    if (!$id) {
        echo "Usage: php simulate_archive.php --restore <account_id>\n";
        exit(1);
    }
    $acc = CustomerAccount::find($id);
    if (!$acc) {
        echo "ERROR: No account with id {$id}.\n";
        exit(1);
    }
    if (!$acc->archived_at) {
        echo "Account #{$id} ({$acc->client_name}) is not archived.\n";
        exit(0);
    }
    $acc->update(['archived_at' => null]);
    echo "Restored: {$acc->client_name} ({$acc->control_number})\n";
    exit(0);
}

// ─── LIST / ELIGIBLE BOOKINGS ──────────────────────────────────────────────────
function eligibleBookings(): \Illuminate\Support\Collection
{
    return Booking::where('status', 'completed')
        ->where('deliverables_unlocked', true)
        ->whereNotNull('delivered_at')
        ->whereHas('customerAccount', fn ($q) => $q->whereNull('archived_at'))
        ->with('customerAccount')
        ->get();
}

$bookings = eligibleBookings();

echo "============================================================\n";
echo "  CLIENT ACCOUNT ARCHIVING SIMULATOR\n";
echo "  Auto-archive window: {$autoDeleteDays} day(s) after delivery\n";
echo "============================================================\n";

if ($bookings->isEmpty()) {
    echo "No delivered/completed bookings with active client accounts.\n";
    echo "TIP: Create + deliver a booking first, or run with a booking_ref/id to backdate one.\n";
    exit(0);
}

echo "\nEligible bookings:\n";
foreach ($bookings as $b) {
    $daysAgo = (int) $b->delivered_at->diffInDays(now());
    $past = $b->delivered_at->lte(now()->subDays($autoDeleteDays));
    printf(
        "  [#%d] %s | %s | delivered %s (%d day%s ago) %s\n",
        $b->id,
        $b->booking_ref,
        $b->client_name,
        $b->delivered_at->format('M d, Y H:i'),
        $daysAgo,
        $daysAgo === 1 ? '' : 's',
        $past ? '<-- past threshold, WILL be archived' : ''
    );
}
echo "\n";

// ─── TARGET SELECTION ──────────────────────────────────────────────────────────
$identifier = $args[0] ?? null;
$targets = $bookings;

if ($identifier && $identifier !== '--all') {
    $byRef = $bookings->first(fn ($b) => $b->booking_ref === $identifier);
    $byId  = is_numeric($identifier) ? $bookings->first(fn ($b) => $b->id == $identifier) : null;
    $byAcct = CustomerAccount::where('control_number', $identifier)
        ->orWhere('client_email', $identifier)
        ->first();
    $targets = collect();
    if ($byRef) $targets->push($byRef);
    elseif ($byId) $targets->push($byId);
    elseif ($byAcct && $bookings->contains('id', $byAcct->booking_id)) {
        $targets->push($bookings->firstWhere('id', $byAcct->booking_id));
    } else {
        echo "ERROR: No eligible booking found for '{$identifier}'.\n";
        exit(1);
    }
} elseif ($identifier !== '--all') {
    echo "Enter booking id (or press Enter for all): ";
    $choice = trim(fgets(STDIN));
    if ($choice !== '') {
        $targets = $bookings->where('id', (int) $choice)->values();
        if ($targets->isEmpty()) {
            echo "ERROR: No eligible booking with id {$choice}.\n";
            exit(1);
        }
    }
}

echo "WARNING: This will backdate delivery (past the {$autoDeleteDays}-day window) and ARCHIVE the account(s).\n";
foreach ($targets as $b) {
    echo "  - {$b->booking_ref} | {$b->client_name}\n";
}
echo "Type 'yes' to continue: ";

if (trim(fgets(STDIN)) !== 'yes') {
    echo "Cancelled.\n";
    exit(0);
}

// ─── SIMULATE ──────────────────────────────────────────────────────────────────
foreach ($targets as $b) {
    // Make sure the booking satisfies the archiving query, backdating delivery if needed.
    $updates = ['status' => 'completed', 'deliverables_unlocked' => true];
    if (!$b->delivered_at || !$b->delivered_at->lte(now()->subDays($autoDeleteDays))) {
        $updates['delivered_at'] = now()->subDays($autoDeleteDays + 1);
        $oldDate = $b->delivered_at ? $b->delivered_at->format('M d, Y H:i') : 'null';
        echo "  Backdating delivery for {$b->booking_ref}: {$oldDate} -> " . $updates['delivered_at']->format('M d, Y H:i') . "\n";
    }
    $b->update($updates);
}

echo "\nRunning command: php artisan accounts:archive-expired\n";
Artisan::call('accounts:archive-expired');
echo Artisan::output();

// ─── VERIFY ────────────────────────────────────────────────────────────────────
echo "\nVerification:\n";
foreach ($targets as $b) {
    $acc = $b->customerAccount()->first();
    echo "  {$b->booking_ref} | {$b->client_name} | archived_at: " . ($acc?->archived_at?->format('M d, Y H:i') ?? 'NULL (not archived!)') . "\n";
}

echo "\nDone.\n";
echo "Restore with: php simulate_archive.php --restore <account_id>\n";
