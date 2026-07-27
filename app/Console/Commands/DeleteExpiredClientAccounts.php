<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\CustomerAccount;
use App\Models\Setting;
use App\Models\Downpayment;
use App\Models\PostProductionTask;
use Illuminate\Console\Command;

class DeleteExpiredClientAccounts extends Command
{
    protected $signature = 'accounts:delete-expired';
    protected $description = 'Delete client accounts for delivered bookings past the auto-delete threshold';

    public function handle(): int
    {
        $days = (int) Setting::getValue('client_auto_delete_days', '30');
        $threshold = now()->subDays($days);

        $bookings = Booking::where('status', 'completed')
            ->where('deliverables_unlocked', true)
            ->whereNotNull('delivered_at')
            ->where('delivered_at', '<=', $threshold)
            ->with('customerAccount')
            ->get();

        $deletedCount = 0;

        foreach ($bookings as $booking) {
            if (!$booking->customerAccount) {
                continue;
            }

            $account = $booking->customerAccount;

            // Delete related data
            PostProductionTask::whereHas('postProduction', fn($q) => $q->where('booking_id', $booking->id))->delete();
            Downpayment::where('booking_id', $booking->id)->delete();

            // Delete the customer account
            $account->delete();

            $deletedCount++;

            $this->info("Deleted account for: {$booking->client_name} ({$booking->booking_ref})");
        }

        if ($deletedCount === 0) {
            $this->info('No expired client accounts found.');
        } else {
            $this->info("Successfully deleted {$deletedCount} client account(s).");
        }

        return Command::SUCCESS;
    }
}
