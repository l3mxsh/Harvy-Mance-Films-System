<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\CustomerAccount;
use App\Models\Setting;
use App\Models\Downpayment;
use App\Models\PostProductionTask;
use App\Models\StaffSchedule;
use App\Models\RescheduleRequest;
use App\Models\CancellationRequest;
use App\Models\BookingItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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

            DB::transaction(function () use ($booking, &$deletedCount) {
                $account = $booking->customerAccount;

                if ($booking->postProduction) {
                    PostProductionTask::where('post_production_id', $booking->postProduction->id)->delete();
                    $booking->postProduction->delete();
                }

                $downpayments = Downpayment::where('booking_id', $booking->id)->get();
                foreach ($downpayments as $dp) {
                    if ($dp->payment_proof && Storage::disk('public')->exists($dp->payment_proof)) {
                        Storage::disk('public')->delete($dp->payment_proof);
                    }
                    $dp->delete();
                }

                CancellationRequest::where('booking_id', $booking->id)->each(function ($c) {
                    if ($c->refund_proof && Storage::disk('public')->exists($c->refund_proof)) {
                        Storage::disk('public')->delete($c->refund_proof);
                    }
                    $c->delete();
                });

                RescheduleRequest::where('booking_id', $booking->id)->delete();
                StaffSchedule::where('booking_id', $booking->id)->delete();
                BookingItem::where('booking_id', $booking->id)->delete();
                $booking->addons()->detach();

                $account->delete();
                $deletedCount++;

                $this->info("Deleted: {$booking->client_name} ({$booking->booking_ref})");
            });
        }

        $this->info($deletedCount === 0
            ? 'No expired client accounts found.'
            : "Deleted {$deletedCount} client account(s).");

        return Command::SUCCESS;
    }
}
