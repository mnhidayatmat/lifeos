<?php

namespace App\Notifications;

use App\Models\Achievement;
use Illuminate\Notifications\Notification;

class AchievementNotification extends Notification
{
    public function __construct(private Achievement $achievement) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'achievement',
            'title' => 'Achievement Unlocked!',
            'message' => $this->achievement->name . ' — ' . $this->achievement->description,
            'achievement_key' => $this->achievement->key,
            'xp_reward' => $this->achievement->xp_reward,
        ];
    }
}
