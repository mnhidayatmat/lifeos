# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

LifeOS is a personal productivity platform combining goal management (Life Areas → Goals → Projects → Tasks) with an RPG-inspired progression system (XP, 8 stats, 6 ranks, achievements, streaks). Design philosophy: "Claude outside, Solo Leveling inside" — clean/premium/minimal UI with a motivating progression system underneath.

## Commands

```bash
# Development (runs server, queue, logs, vite concurrently)
composer dev

# Build frontend assets
npm run build

# Run tests (clears config first, uses in-memory SQLite)
composer test

# Run a single test file
php artisan test --filter=ExampleTest

# Lint PHP
./vendor/bin/pint

# Fresh setup
composer setup

# Run migrations
php artisan migrate

# Seed achievements
php artisan db:seed --class=AchievementSeeder
```

## Architecture

### Stack
Laravel 13 + Blade + Tailwind CSS 3 + Alpine.js. No SPA — all server-rendered with Alpine for interactivity (modals, dropdowns, inline edits). Vite for bundling. SQLite in development.

### Domain Model Hierarchy
**Life Areas** → **Goals** → **Projects** → **Tasks** (→ Subtasks). Tasks can also be standalone (no goal/project required). Each Life Area maps to a primary + secondary stat.

### Progression System (Event-Driven)
The core progression loop is event-driven, registered in `AppServiceProvider::boot()`:

1. User completes a task → `TaskCompleted` event fires
2. Listeners: `AwardTaskXp` (5/15/30 XP by effort, 1.2x if goal-linked, 70/30 primary/secondary stat split), `UpdateStreak`, `CheckAchievements`
3. If XP crosses level threshold → `LevelUp` event → `CreateLevelUpNotification`
4. If achievement condition met → `AchievementUnlocked` event → `CreateAchievementNotification`
5. Reviews and streak milestones have their own parallel event chains

**Key services:** `XpService` (XP math, level formula: `floor(sqrt(totalXp / 25)) + 1`), `StreakService` (consecutive days with 1-day grace), `AchievementService` (8 conditions), `RankService` (level → rank mapping), `ProgressService` (goal progress calculation).

### Authorization Pattern
Manual `abort_unless($model->user_id === auth()->id(), 403)` in a private `authorize()` method on each controller. No policies.

### Onboarding Flow
4-step flow: archetype selection → life areas → first goal → welcome. Gated by `EnsureOnboardingComplete` middleware on all main routes. Archetypes (Student/Researcher/Founder/Professional/Creator) pre-seed life areas with stat mappings via `OnboardingService`.

### UI Components
Blade components in `resources/views/components/ui/`: `card`, `badge`, `modal` (Alpine-driven, opened via `$dispatch('open-modal-{name}')`), `empty-state`, `progress-bar`, `stat-bar`, `rank-badge`, `level-up-modal`, `effort-badge`, `toast`. Icon component at `components/icon.blade.php`.

### Tailwind Design Tokens
Custom colors in `tailwind.config.js`: `stat.*` (8 stat colors) and `rank.*` (6 rank colors). Primary brand color: indigo-600. Font: Inter.

### Layouts
- `layouts/app.blade.php` — authenticated shell with sidebar + topbar, wrapped in `x-data="{ sidebarOpen: false }"`
- `layouts/guest.blade.php` — split-screen auth layout (indigo gradient left, form right)
- `layouts/sidebar.blade.php` — fixed 64px sidebar with nav links
- `layouts/topbar.blade.php` — sticky topbar with notifications + user menu

### Database
17 migrations. Key models beyond the hierarchy: `UserStat` (8 per user), `XpLog` (audit trail), `Streak`, `Achievement`/`UserAchievement`, `Review` (daily/weekly with JSON responses), `Habit`/`HabitLog`, `Vision`, `IdentityTrait`, `Resource`.

### Project Documentation
Detailed MVP workflow at `claudedocs/workflow_mvp.md` covering all 7 implementation phases.
