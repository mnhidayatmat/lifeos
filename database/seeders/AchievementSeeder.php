<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        // Milestones — meaningful, professional accomplishments. No points, no ranks.
        $milestones = [
            [
                'key' => 'first_task',
                'name' => 'First task done',
                'description' => 'Complete your first task',
                'icon' => 'check-square',
            ],
            [
                'key' => 'first_goal',
                'name' => 'First goal achieved',
                'description' => 'Complete your first goal',
                'icon' => 'target',
            ],
            [
                'key' => 'first_project',
                'name' => 'First project shipped',
                'description' => 'Complete your first project',
                'icon' => 'folder',
            ],
            [
                'key' => 'streak_7',
                'name' => 'One week consistent',
                'description' => 'Stay active 7 days in a row',
                'icon' => 'trophy',
            ],
            [
                'key' => 'streak_30',
                'name' => 'One month consistent',
                'description' => 'Stay active 30 days in a row',
                'icon' => 'trophy',
            ],
            [
                'key' => 'first_weekly_review',
                'name' => 'First weekly review',
                'description' => 'Complete your first weekly review',
                'icon' => 'book-open',
            ],
            [
                'key' => 'all_areas_active',
                'name' => 'Balanced across areas',
                'description' => 'Keep goals active in all your life areas',
                'icon' => 'grid',
            ],
            [
                'key' => 'tasks_100',
                'name' => '100 tasks completed',
                'description' => 'Complete 100 tasks',
                'icon' => 'check-square',
            ],
        ];

        foreach ($milestones as $milestone) {
            Achievement::updateOrCreate(
                ['key' => $milestone['key']],
                $milestone
            );
        }

        // Remove any milestones from the old gamified set that no longer apply.
        Achievement::whereNotIn('key', array_column($milestones, 'key'))->delete();
    }
}
