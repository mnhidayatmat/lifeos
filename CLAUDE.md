# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

LifeOS is a personal productivity platform built around a clear goal-management hierarchy (Life Areas → Goals → Projects → Tasks) plus reviews, habits, vision, and analytics. Design philosophy: clean / premium / minimal, benchmarked against tools like Linear, Todoist, and Things — progress is shown through real metrics (completion trends, life-area balance, consistency streaks, milestones), not points or levels.

> Note: an earlier version had an RPG/XP layer (experience points, levels, ranks, 8 character stats). That layer was fully removed in favor of professional progress tracking. "Consistency" streaks and "Milestones" (formerly achievements, no point rewards) are kept.

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

# Seed milestones (table is still named `achievements`)
php artisan db:seed --class=AchievementSeeder
```

## Architecture

### Stack
Laravel 13 + Blade + Tailwind CSS 3 + Alpine.js. No SPA — all server-rendered with Alpine for interactivity (modals, dropdowns, inline edits). Vite for bundling. MySQL 8 in development (tests use in-memory SQLite).

### Domain Model Hierarchy
**Life Areas** → **Goals** → **Projects** → **Tasks** (→ Subtasks). Tasks can also be standalone (no goal/project required).

### Event-Driven Progress Tracking
A light event loop is registered in `AppServiceProvider::boot()`:

1. User completes a task → `TaskCompleted` event fires
2. Listeners: `UpdateStreak` (consistency streak, consecutive days with 1-day grace) and `CheckAchievements` (milestone unlocks)
3. If a milestone condition is met → `AchievementUnlocked` event → `CreateAchievementNotification`
4. Reviews fire `ReviewCompleted` → `CheckAchievements`

There is **no** XP / level / rank math. `AwardTaskXp`, `XpService`, `RankService`, `LevelUp`, `StreakMilestoneReached`, and the level-up notification were removed.

**Key services:** `StreakService` (consecutive days with 1-day grace), `AchievementService` (milestone conditions, no rewards), `ProgressService` (goal progress calculation), `ReviewService` (auto-summaries from real task/goal counts). Analytics is computed in `AnalyticsController` from task completions, goal/project counts, life-area balance, and streaks.

### Authorization Pattern
Manual `abort_unless($model->user_id === auth()->id(), 403)` in a private `authorize()` method on each controller. No policies.

### Onboarding Flow
4-step flow: template selection → life areas → first goal → welcome. Gated by `EnsureOnboardingComplete` middleware on all main routes. Onboarding templates (Student/Researcher/Founder/Professional/Creator) pre-seed life areas via `OnboardingService` (stored on `users.archetype`). No stat mappings.

### UI Components
Blade components in `resources/views/components/ui/`: `card`, `badge`, `modal` (Alpine-driven, opened via `$dispatch('open-modal-{name}')`), `empty-state`, `progress-bar`, `effort-badge`, `toast`. Icon component at `components/icon.blade.php`. (The RPG `stat-bar`, `rank-badge`, and `level-up-modal` components were removed.)

### Tailwind Design Tokens
`tailwind.config.js` only sets the Inter font family now; the old `stat.*` / `rank.*` custom color palettes were removed. Primary brand color: indigo-600.

### Layouts
- `layouts/app.blade.php` — authenticated shell with sidebar + topbar, wrapped in `x-data="{ sidebarOpen: false }"`
- `layouts/guest.blade.php` — split-screen auth layout (indigo gradient left, form right)
- `layouts/sidebar.blade.php` — fixed sidebar (desktop) / slide-in drawer (mobile, toggled by `sidebarOpen`)
- `layouts/topbar.blade.php` — sticky topbar with notifications + user menu
- `layouts/bottom-nav.blade.php` — mobile-only bottom tab bar (thumb-zone nav: Home/Tasks/Goals/Projects/More); `lg:hidden`, hides while the drawer is open. Modals (`components/ui/modal`) render as bottom sheets on mobile and centered dialogs on desktop.

### Database
Key models beyond the hierarchy: `Streak` (consistency), `Achievement`/`UserAchievement` (milestones — table names kept), `Review` (daily/weekly/monthly with JSON responses), `Habit`/`HabitLog`, `Vision`, `IdentityTrait`, `Resource`. The `user_stats` and `xp_logs` tables and the `total_xp`/`level`/`rank`/`title` (users), `primary_stat`/`secondary_stat` (life_areas), `xp_awarded` (tasks/reviews/resources/habit_logs), `linked_stat` (identity_traits), and `xp_reward` (achievements) columns were dropped in `2026_06_11_000000_remove_gamification_fields`.

### Project Documentation
Detailed MVP workflow at `claudedocs/workflow_mvp.md` covering all 7 implementation phases.
