<?php

namespace App\Models;

use App\Notifications\BrevoResetPassword;
use App\Notifications\BrevoVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'password', 'role', 'archetype', 'calendar_token', 'onboarding_completed_at', 'google_id', 'avatar', 'auth_type'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, \Illuminate\Notifications\Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'onboarding_completed_at' => 'datetime',
            'password' => 'hashed',
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

    public function vision(): HasOne
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

    public function importantDates(): HasMany
    {
        return $this->hasMany(ImportantDate::class);
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

    /**
     * The secret token used to subscribe to this user's calendar (.ics) feed.
     * Generated lazily on first access.
     */
    public function calendarToken(): string
    {
        if (! $this->calendar_token) {
            $this->forceFill(['calendar_token' => Str::random(48)])->save();
        }

        return $this->calendar_token;
    }
}
