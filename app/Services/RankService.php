<?php

namespace App\Services;

class RankService
{
    public function resolveRank(int $level): string
    {
        return match (true) {
            $level >= 76 => 'legend',
            $level >= 51 => 'master',
            $level >= 31 => 'expert',
            $level >= 16 => 'specialist',
            $level >= 6 => 'apprentice',
            default => 'initiate',
        };
    }
}
