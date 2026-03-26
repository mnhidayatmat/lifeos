<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Notifications\BrevoResetPassword;
use App\Notifications\BrevoVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[Fillable(['name', 'email', 'password', 'role', 'archetype', 'onboarding_completed_at', 'total_xp', 'level', 'rank', 'title'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, \Illuminate\Notifications\Notifiable;

    public const STATS = [
        'discipline', 'focus', 'knowledge', 'strength',
        'wealth', 'creativity', 'influence', 'wisdom',
    ];

    public const RANKS = [
        'initiate', 'apprentice', 'specialist', 'expert', 'master', 'legend',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'onboarding_completed_at' => 'datetime',
            'password' => 'hashed',
            'total_xp' => 'integer',
            'level' => 'integer',
        ];
    }

    // Relationships

    public function lifeAreas(): HasMany
    {
        return $this->hasMany(LifeArea::class)->orderBy('sort_order');
    }

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function stats(): HasMany
    {
        return $this->hasMany(UserStat::class);
    }

    public function xpLogs(): HasMany
    {
        return $this->hasMany(XpLog::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }

    public function streaks(): HasMany
    {
        return $this->hasMany(Streak::class);
    }

    public function habits(): HasMany
    {
        return $this->hasMany(Habit::class)->orderBy('sort_order');
    }

    public function vision(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Vision::class);
    }

    public function identityTraits(): HasMany
    {
        return $this->hasMany(IdentityTrait::class)->orderBy('sort_order');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    // Brevo email overrides

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new BrevoVerifyEmail);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new BrevoResetPassword($token));
    }

    // Helpers

    public function hasCompletedOnboarding(): bool
    {
        return $this->onboarding_completed_at !== null;
    }

    public function initializeStats(): void
    {
        foreach (self::STATS as $stat) {
            $this->stats()->firstOrCreate(['stat' => $stat], ['total_xp' => 0]);
        }
    }

    public function xpForNextLevel(): int
    {
        return ($this->level) ** 2 * 25;
    }

    public function xpForCurrentLevel(): int
    {
        return ($this->level - 1) ** 2 * 25;
    }

    public function xpProgress(): int
    {
        return $this->total_xp - $this->xpForCurrentLevel();
    }

    public function xpNeeded(): int
    {
        return $this->xpForNextLevel() - $this->xpForCurrentLevel();
    }
}
