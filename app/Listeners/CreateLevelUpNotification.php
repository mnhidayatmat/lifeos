<?php

namespace App\Listeners;

use App\Events\LevelUp;

class CreateLevelUpNotification
{
    public function handle(LevelUp $event): void
    {
        // Database notification
        $data = [
            'type' => 'level_up',
            'title' => "Level {$event->newLevel} reached!",
            'message' => $event->rankChanged()
                ? "You've reached the rank of " . ucfirst($event->newRank) . "!"
                : "Keep going — you're getting stronger.",
            'level' => $event->newLevel,
            'rank' => $event->newRank,
        ];

        $event->user->notify(new \App\Notifications\LevelUpNotification($data));

        // Flash for level-up modal display
        session()->flash('level_up', [
            'level' => $event->newLevel,
            'rank' => $event->newRank,
            'rank_changed' => $event->rankChanged(),
        ]);
    }
}
