<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            [
                'key' => 'first_task',
                'name' => 'First Step',
                'description' => 'Complete your first task',
                'icon' => 'check-square',
                'xp_reward' => 10,
            ],
            [
                'key' => 'first_goal',
                'name' => 'Goal Setter',
                'description' => 'Complete your first goal',
                'icon' => 'target',
                'xp_reward' => 50,
            ],
            [
                'key' => 'streak_7',
                'name' => 'On Fire',
                'description' => 'Maintain a 7-day task streak',
                'icon' => 'trophy',
                'xp_reward' => 20,
            ],
            [
                'key' => 'streak_30',
                'name' => 'Unstoppable',
                'description' => 'Maintain a 30-day task streak',
                'icon' => 'trophy',
                'xp_reward' => 100,
            ],
            [
                'key' => 'first_weekly_review',
                'name' => 'Reflector',
                'description' => 'Complete your first weekly review',
                'icon' => 'book-open',
                'xp_reward' => 25,
            ],
            [
                'key' => 'reached_apprentice',
                'name' => 'Rising Up',
                'description' => 'Reach the Apprentice rank',
                'icon' => 'trophy',
                'xp_reward' => 30,
            ],
            [
                'key' => 'all_areas_active',
                'name' => 'Life in Balance',
                'description' => 'Have activity in all your life areas',
                'icon' => 'grid',
                'xp_reward' => 40,
            ],
            [
                'key' => 'tasks_100',
                'name' => 'Centurion',
                'description' => 'Complete 100 tasks',
                'icon' => 'check-square',
                'xp_reward' => 100,
            ],
        ];

        foreach ($achievements as $achievement) {
            Achievement::updateOrCreate(
                ['key' => $achievement['key']],
                $achievement
            );
        }
    }
}
