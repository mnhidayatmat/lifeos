<?php

namespace App\Notifications;

use App\Models\ImportantDate;
use Illuminate\Notifications\Notification;

class ImportantDateReminder extends Notification
{
    public function __construct(
        private ImportantDate $date,
        private int $daysBefore,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $when = match (true) {
            $this->daysBefore === 0 => 'is today',
            $this->daysBefore === 1 => 'is tomorrow',
            default => "is in {$this->daysBefore} days",
        };

        return [
            'type' => 'important_date',
            'title' => 'Upcoming: '.$this->date->title,
            'message' => $this->date->title.' '.$when.' ('.$this->date->nextOccurrence()->format('M j').').',
            'important_date_id' => $this->date->id,
        ];
    }
}
