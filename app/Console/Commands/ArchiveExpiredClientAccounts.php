<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Console\Command;

class ArchiveExpiredClientAccounts extends Command
{
    protected $signature = 'accounts:archive-expired';
    protected $description = 'Archive client accounts for delivered bookings past the auto-archive threshold';

    public function handle(): int
    {
        $days = (int) Setting::getValue('client_auto_delete_days', '30');
        $threshold = now()->subDays($days);

        $bookings = Booking::where('status', 'completed')
            ->where('deliverables_unlocked', true)
            ->whereNotNull('delivered_at')
            ->where('delivered_at', '<=', $threshold)
            ->whereHas('customerAccount', fn ($q) => $q->whereNull('archived_at'))
            ->with('customerAccount')
            ->get();

        $archivedCount = 0;

        foreach ($bookings as $booking) {
            $account = $booking->customerAccount;

            if (!$account) {
                continue;
            }

            $account->update(['archived_at' => now()]);
            $archivedCount++;

            $this->info("Archived: {$booking->client_name} ({$booking->booking_ref})");
        }

        $this->info($archivedCount === 0
            ? 'No client accounts past the archive threshold.'
            : "Archived {$archivedCount} client account(s).");

        return Command::SUCCESS;
    }
}
