<?php

namespace App\Services;

use App\Models\LifeArea;
use App\Models\User;

class OnboardingService
{
    public const ARCHETYPE_AREAS = [
        'student' => ['Learning', 'Research', 'Health', 'Personal', 'Finance'],
        'researcher' => ['Research', 'Learning', 'Work', 'Health', 'Personal'],
        'founder' => ['Business', 'Work', 'Finance', 'Health', 'Learning'],
        'professional' => ['Work', 'Finance', 'Health', 'Family', 'Learning'],
        'creator' => ['Personal', 'Business', 'Learning', 'Health', 'Work'],
    ];

    public function seedAreasForArchetype(User $user, string $archetype): void
    {
        $areaNames = self::ARCHETYPE_AREAS[$archetype] ?? self::ARCHETYPE_AREAS['professional'];
        $presets = collect(LifeArea::PRESET_AREAS)->keyBy('name');

        foreach ($areaNames as $index => $name) {
            $preset = $presets[$name] ?? null;
            if (!$preset) {
                continue;
            }

            $user->lifeAreas()->create([
                'name' => $preset['name'],
                'slug' => strtolower($preset['name']),
                'color' => $preset['color'],
                'icon' => $preset['icon'],
                'is_preset' => true,
                'is_active' => true,
                'sort_order' => $index,
                'primary_stat' => $preset['primary_stat'],
                'secondary_stat' => $preset['secondary_stat'],
            ]);
        }
    }

    public function completeOnboarding(User $user): void
    {
        $user->update(['onboarding_completed_at' => now()]);
        $user->initializeStats();
    }
}
