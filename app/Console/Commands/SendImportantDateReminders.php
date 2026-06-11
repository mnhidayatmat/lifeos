<?php

namespace App\Console\Commands;

use App\Models\ImportantDate;
use App\Notifications\ImportantDateReminder;
use Illuminate\Console\Command;

class SendImportantDateReminders extends Command
{
    protected $signature = 'important-dates:send-reminders';

    protected $description = 'Send in-app reminders for important dates whose reminder offset falls today.';

    public function handle(): int
    {
        $sent = 0;

        // Only dates still ahead (or today) with reminders configured can fire.
        ImportantDate::with('user')
            ->whereNull('completed_at')
            ->whereNotNull('reminders')
            ->chunkById(200, function ($dates) use (&$sent) {
                foreach ($dates as $date) {
                    $daysUntil = $date->daysUntil();

                    if ($daysUntil < 0) {
                        continue;
                    }

                    // Fire when today matches one of the "days before" offsets.
                    if ($date->reminderOffsets()->contains($daysUntil)) {
                        $date->user?->notify(new ImportantDateReminder($date, $daysUntil));
                        $sent++;
                    }
                }
            });

        $this->info("Sent {$sent} important-date reminder(s).");

        return self::SUCCESS;
    }
}
