<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

class LevelUp
{
    use Dispatchable;

    public function __construct(
        public User $user,
        public int $newLevel,
        public int $oldLevel,
        public string $newRank,
        public string $oldRank,
    ) {}

    public function rankChanged(): bool
    {
        return $this->newRank !== $this->oldRank;
    }
}
