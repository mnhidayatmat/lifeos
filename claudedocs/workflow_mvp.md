# Life OS — MVP Implementation Workflow

> Generated: 2026-03-25
> Stack: Laravel 11 + Blade + Tailwind CSS + Alpine.js
> Estimated Phases: 7 | Estimated Tasks: ~65

---

## Implementation Strategy

**Approach:** Bottom-up, layer by layer
- Phase 1–2: Foundation (install, auth, DB, models)
- Phase 3–4: Core CRUD (life areas, goals, projects, tasks)
- Phase 5: Dashboard (the daily control centre)
- Phase 6: Progression system (XP, stats, ranks, achievements)
- Phase 7: Reviews, notifications, onboarding, polish

**Principle:** Each phase produces a working, testable increment. No phase depends on future phases.

**Dependency chain:**
```
Phase 1 → Phase 2 → Phase 3 → Phase 4 → Phase 5
                                  ↓
                              Phase 6 → Phase 7
```

---

## Phase 1 — Project Scaffolding
**Goal:** Working Laravel app with auth, Tailwind, and base layout
**Dependency:** None
**Checkpoint:** User can register, login, see empty dashboard shell

### Tasks

#### 1.1 Laravel Installation
- [ ] Install Laravel 11 via Composer (`composer create-project laravel/laravel .`)
- [ ] Configure `.env` (app name, DB connection, mail settings)
- [ ] Verify `php artisan serve` works

#### 1.2 Authentication
- [ ] Install Laravel Breeze (`composer require laravel/breeze --dev`)
- [ ] Run `php artisan breeze:install blade` (Blade + Tailwind stack)
- [ ] Run migrations for default auth tables
- [ ] Verify register/login/logout works

#### 1.3 Base Layout
- [ ] Customize `layouts/app.blade.php` — add sidebar structure
- [ ] Create sidebar component with navigation links:
  - Dashboard, Life Areas, Goals, Projects, Tasks, Reviews, Profile
- [ ] Create topbar component with:
  - Breadcrumb area, notification bell placeholder, user menu
- [ ] Set Tailwind theme: neutral palette, premium spacing, clean typography
- [ ] Add Alpine.js for sidebar toggle (mobile)
- [ ] Create `layouts/onboarding.blade.php` — minimal centered layout

#### 1.4 UI Foundation
- [ ] Create reusable Blade components:
  - `x-ui-modal` (Alpine.js driven)
  - `x-ui-badge` (color variants)
  - `x-ui-progress-bar` (percentage fill)
  - `x-ui-empty-state` (icon + message + CTA)
  - `x-ui-toast` (success/info/warning, auto-dismiss)
  - `x-ui-dropdown` (Alpine.js)
- [ ] Define Tailwind color tokens for:
  - Primary (indigo), Success (emerald), Warning (amber), Danger (rose)
  - Stat colors: one per stat (8 colors)
  - Rank colors: one per rank tier (6 colors)

### Checkpoint 1
```
✓ Laravel installed and running
✓ User can register + login
✓ Sidebar + topbar layout renders
✓ Reusable UI components ready
✓ Tailwind theme configured
```

---

## Phase 2 — Database & Models
**Goal:** All migrations run, all models defined with relationships
**Dependency:** Phase 1
**Checkpoint:** `php artisan migrate` creates all tables, models have working relationships

### Tasks

#### 2.1 Migrations (create in this order)
- [ ] `create_life_areas_table` — all fields per schema
- [ ] `create_goals_table` — with life_area_id FK
- [ ] `create_projects_table` — with life_area_id + goal_id FKs
- [ ] `create_tasks_table` — with project_id + goal_id + parent_task_id FKs
- [ ] `create_subtasks_table` — with task_id FK
- [ ] `create_user_stats_table` — with unique (user_id, stat)
- [ ] `create_xp_logs_table` — with index on (user_id, created_at)
- [ ] `create_reviews_table` — with unique (user_id, type, period_date)
- [ ] `create_achievements_table` — with unique key
- [ ] `create_user_achievements_table` — with unique (user_id, achievement_id)
- [ ] `create_streaks_table` — with unique (user_id, type)
- [ ] `add_progression_fields_to_users_table` — total_xp, level, rank, title, archetype, onboarding_completed_at

#### 2.2 Models
- [ ] `LifeArea` model — relationships: belongsTo User, hasMany Goals, hasMany Projects
- [ ] `Goal` model — relationships: belongsTo User, belongsTo LifeArea, hasMany Projects, hasMany Tasks
- [ ] `Project` model — relationships: belongsTo User, belongsTo LifeArea, belongsTo Goal (nullable), hasMany Tasks
- [ ] `Task` model — relationships: belongsTo User/Project/Goal (nullable), hasMany Subtasks, belongsTo parentTask (nullable)
- [ ] `Subtask` model — relationships: belongsTo Task
- [ ] `UserStat` model — relationships: belongsTo User
- [ ] `XpLog` model — relationships: belongsTo User
- [ ] `Review` model — relationships: belongsTo User, cast responses/auto_summary to array
- [ ] `Achievement` model — relationships: belongsToMany Users through user_achievements
- [ ] `UserAchievement` model — pivot with unlocked_at
- [ ] `Streak` model — relationships: belongsTo User
- [ ] Update `User` model — add hasMany for all owned models, rank cast to enum, progression accessors

#### 2.3 Seeders
- [ ] `AchievementSeeder` — seed MVP achievement definitions:
  - first_task, first_goal, streak_7, streak_30, first_weekly_review, reached_apprentice, all_areas_active, tasks_100
- [ ] `DefaultLifeAreaSeeder` — reference data for archetype templates (not run on migrate, used by OnboardingService)

### Checkpoint 2
```
✓ php artisan migrate runs cleanly
✓ php artisan db:seed populates achievements
✓ All model relationships return correct data (tinker test)
✓ No FK constraint errors
```

---

## Phase 3 — Life Areas & Goals CRUD
**Goal:** Users can manage life areas and goals
**Dependency:** Phase 2
**Checkpoint:** User can create/edit/delete life areas and goals, see progress

### Tasks

#### 3.1 Life Areas
- [ ] `LifeAreaController` — index, store, update, destroy, toggle, reorder
- [ ] Routes in `web.php` under auth middleware
- [ ] `EnsureOnboardingComplete` middleware — redirect to /onboarding if not completed (skip for now, wire in Phase 7)
- [ ] Life Areas index page:
  - Grid/list of areas with color, icon, stat badges
  - Active/inactive toggle
  - Sort drag (Alpine.js sortable, or simple up/down arrows for MVP)
  - "Add custom area" button → modal form
- [ ] Life Area create/edit form (modal):
  - Name, color picker, primary stat dropdown, secondary stat dropdown
  - Validation: max 10 total, max 8 active
- [ ] Life Area delete confirmation modal
- [ ] Soft cap enforcement: prevent activating > 8

#### 3.2 Goals
- [ ] `GoalController` — index, create, store, show, edit, update, destroy, updateStatus, updateProgress
- [ ] Routes under auth middleware
- [ ] Goals index page:
  - Filter by life area (tabs or dropdown)
  - Goal cards: title, life area badge, progress bar, status pill, due date
  - "Add goal" button
- [ ] Goal create/edit page:
  - Title, description, life area select, progress type radio (task/KPI/manual)
  - If KPI: target_value input
  - Priority, due date, status
- [ ] Goal detail page:
  - Progress bar (computed)
  - Linked projects list
  - Linked tasks list
  - Status change dropdown
  - KPI/manual progress updater
- [ ] `ProgressService` — implement `computeGoalProgress()`
- [ ] Goal status change: completed → set completed_at, abandoned → archive gracefully

### Checkpoint 3
```
✓ User can create custom life areas with stat mappings
✓ User can CRUD goals under life areas
✓ Goal progress computes correctly for all 3 modes
✓ Life area cap enforced (max 8 active)
✓ Status transitions work (including abandon/archive)
```

---

## Phase 4 — Projects & Tasks CRUD
**Goal:** Full task management working, including subtasks and recurring
**Dependency:** Phase 3
**Checkpoint:** User can manage projects, tasks, subtasks; complete tasks; recurring tasks generate

### Tasks

#### 4.1 Projects
- [ ] `ProjectController` — index, store, show, update, destroy, updateStatus
- [ ] Routes under auth middleware
- [ ] Projects index page:
  - Filter by life area / goal
  - Project cards: title, area badge, goal link, task count, progress, status
- [ ] Project create/edit form:
  - Title, description, life area (required), goal (optional dropdown filtered by area), priority, due date
- [ ] Project detail page:
  - Task list within project
  - Inline task creation
  - Progress summary (tasks completed / total)

#### 4.2 Tasks
- [ ] `TaskController` — index, store, show, update, destroy, complete, reopen
- [ ] Routes under auth middleware
- [ ] Tasks index page:
  - Views: Today, Overdue, All, By Project, By Goal
  - Task rows: checkbox, title, effort badge (S/M/L color-coded), priority icon, due date, area/goal pill
  - Inline task creation (title + effort + due date minimum)
- [ ] Task create/edit form (can be modal or full page):
  - Title, description, effort (S/M/L radio), priority, due date
  - Project select (optional), Goal select (optional)
  - Recurring toggle → if yes: recurrence_rule (daily / weekly with day picker / monthly with date)
- [ ] Task completion:
  - PATCH `/tasks/{task}/complete` → sets status=completed, completed_at=now
  - For now, just completes the task — XP awarding comes in Phase 6
  - Checkbox interaction via Alpine.js (immediate visual feedback, async request)
- [ ] Task reopen: revert status to pending, clear completed_at

#### 4.3 Subtasks
- [ ] `SubtaskController` — store, toggle, destroy
- [ ] Display subtask checklist within task detail / task card expansion
- [ ] Toggle subtask via Alpine.js checkbox + async PATCH

#### 4.4 Recurring Tasks
- [ ] `GenerateRecurringTasks` Artisan command:
  - Find all tasks where is_recurring=true and parent_task_id IS NULL
  - Parse recurrence_rule
  - Create new task instance for today if not already created
  - Link via parent_task_id
- [ ] Register command in scheduler: `$schedule->command('tasks:generate-recurring')->daily()`
- [ ] Recurring badge on task cards

### Checkpoint 4
```
✓ Full CRUD for projects and tasks
✓ Tasks can be standalone, project-linked, or goal-linked
✓ Subtask checklist works
✓ Task completion toggles cleanly
✓ Recurring tasks generate daily via scheduler
✓ Filter views: today, overdue, all
```

---

## Phase 5 — Dashboard
**Goal:** Daily control centre with all essential elements
**Dependency:** Phase 4
**Checkpoint:** Dashboard shows today's tasks, overdue, goals, and placeholders for XP/stats

### Tasks

#### 5.1 Dashboard Controller
- [ ] `DashboardController@index` — aggregate data:
  - Today's tasks (due today + recurring for today)
  - Overdue tasks (past due, not completed)
  - Active goals (top 5 by priority/recent activity) with progress
  - User progression data (level, XP, rank — placeholder values until Phase 6)
  - User stats (from user_stats table — placeholder until Phase 6)

#### 5.2 Dashboard Layout
- [ ] Two-column layout (desktop): main content (left 2/3) + progression sidebar (right 1/3)
- [ ] Mobile: stacked single column

#### 5.3 Dashboard Components
- [ ] `dashboard/today-tasks.blade.php`:
  - Task list with inline completion checkboxes
  - Effort badges
  - "Add task" inline form at bottom
  - Empty state: encouraging message
- [ ] `dashboard/overdue-tasks.blade.php`:
  - Collapsed by default, count badge
  - Expandable list
  - Quick reschedule action (change due date)
- [ ] `dashboard/active-goals.blade.php`:
  - Top goals with progress bars
  - Area color coding
  - Link to goal detail
- [ ] `dashboard/xp-bar.blade.php`:
  - Current level number
  - XP progress bar to next level
  - Rank badge
- [ ] `dashboard/stat-summary.blade.php`:
  - 8 stat bars with labels and values
  - Stat-specific colors
- [ ] `dashboard/quick-task.blade.php`:
  - Inline form: title, effort select, due date (default today)
  - Submit creates standalone task

### Checkpoint 5
```
✓ Dashboard loads with real task/goal data
✓ Tasks completable from dashboard
✓ Overdue tasks visible and collapsible
✓ Goal progress bars accurate
✓ XP bar and stat summary render (with real or placeholder data)
✓ Quick task creation works
✓ Responsive on mobile
```

---

## Phase 6 — Progression System
**Goal:** XP, stats, levels, ranks, achievements, streaks all working
**Dependency:** Phase 4 (tasks must be completable)
**Checkpoint:** Completing a task awards XP, updates stats, checks level-up and achievements

### Tasks

#### 6.1 Core Services
- [ ] `XpService`:
  - `awardTaskXp(Task $task)` — calculate XP from effort, apply goal multiplier, resolve stats from life area, split 70/30, log to xp_logs, update user_stats, update user.total_xp
  - `awardReviewXp(Review $review)` — 10 XP daily, 25 XP weekly → Wisdom/Discipline
  - `awardStreakBonus(Streak $streak, int $milestone)` — 20/100/500 XP
  - `calculateLevel(int $totalXp)` — formula: floor(√(totalXp / 25)) + 1
  - `xpForLevel(int $level)` — inverse: (level - 1)² × 25
- [ ] `RankService`:
  - `resolveRank(int $level)` — map level ranges to rank enum
- [ ] `StatService`:
  - `resolveStatsForTask(Task $task)` — walk up: task → project → goal → life_area to find stat mapping
  - `getUserStats(User $user)` — return all 8 stats with XP totals
- [ ] `StreakService`:
  - `recordActivity(User $user, string $type)` — update streak count, check grace
  - `checkMilestones(Streak $streak)` — fire event at 7, 30, 100
  - `expireStale()` — called by scheduler, reset streaks past grace period
- [ ] `AchievementService`:
  - `checkAll(User $user)` — run all checkers
  - Individual checkers: query counts and conditions
  - `award(User $user, Achievement $achievement)` — create pivot, fire event

#### 6.2 Event System
- [ ] Create events: `TaskCompleted`, `LevelUp`, `AchievementUnlocked`, `ReviewCompleted`, `StreakMilestoneReached`
- [ ] Create listeners:
  - `AwardTaskXp` — listens to TaskCompleted, calls XpService
  - `UpdateGoalProgress` — listens to TaskCompleted, calls ProgressService
  - `UpdateStreak` — listens to TaskCompleted, calls StreakService
  - `CheckAchievements` — listens to TaskCompleted + ReviewCompleted + StreakMilestoneReached
  - `UpdateUserRank` — listens to LevelUp, calls RankService
  - `CreateLevelUpNotification` — listens to LevelUp
  - `CreateAchievementNotification` — listens to AchievementUnlocked
  - `AwardReviewXp` — listens to ReviewCompleted
  - `AwardStreakBonus` — listens to StreakMilestoneReached
- [ ] Register all in `EventServiceProvider`

#### 6.3 Wire Into Task Completion
- [ ] Update `TaskController@complete`:
  - After marking task complete, dispatch `TaskCompleted` event
  - Return XP gained data in response (for toast)
- [ ] XP gain toast on dashboard:
  - After task checkbox, show "+15 XP Knowledge" floating toast
  - Alpine.js: intercept response, display toast with stat name + amount

#### 6.4 Level-Up Experience
- [ ] Detect level change in `XpService::awardTaskXp()`:
  - Compare level before and after XP addition
  - If changed, dispatch `LevelUp` event
- [ ] `level-up-modal.blade.php`:
  - Subtle overlay: "Level 5 reached" + new rank if changed
  - Stat increases summary
  - Dismiss after 3s or on click
  - CSS animation: fade in, gentle scale

#### 6.5 Profile / Progression Page
- [ ] `ProfileController@index`:
  - User card: name, level, rank badge, title, total XP
  - Stat bars: all 8 stats with XP values and visual bars
  - XP to next level progress
  - Recent XP log (last 10 entries)
- [ ] `ProfileController@achievements`:
  - All achievements grid
  - Unlocked: highlighted with date
  - Locked: greyed out with description (so user knows what to aim for)
- [ ] Rank badge component:
  - Color-coded per rank tier
  - Display on profile, dashboard, sidebar

#### 6.6 Stat Initialization
- [ ] When user completes onboarding (or on first task), ensure all 8 `user_stats` rows exist
- [ ] Add helper: `User::initializeStats()`

### Checkpoint 6
```
✓ Completing a task awards correct XP to correct stats
✓ Goal-linked tasks get 1.2x multiplier
✓ XP toast appears on task completion
✓ Level calculation works, level-up modal fires
✓ Rank updates on level thresholds
✓ Achievements unlock and notify
✓ Streaks track and award milestones
✓ Profile page shows full stat card + achievements
✓ All formulas match spec (5/15/30, 70/30 split, √ curve)
```

---

## Phase 7 — Reviews, Notifications, Onboarding & Polish
**Goal:** Complete MVP feature set, polished end-to-end experience
**Dependency:** Phase 6
**Checkpoint:** Full user journey works: onboard → plan → execute → review → grow

### Tasks

#### 7.1 Review System
- [ ] `ReviewController` — daily, weekly, submitDaily, submitWeekly, history
- [ ] `ReviewService`:
  - `generateDailyData()` — tasks completed today, XP earned, stat gains
  - `generateWeeklyData()` — tasks completed/overdue/created, goal progress deltas, stat gains, strongest/neglected area, streak summary
  - `completeReview()` — save responses, award XP, update streak, dispatch ReviewCompleted
- [ ] Daily review page:
  - Auto-summary section (what the system knows)
  - Structured prompts (4 questions)
  - Optional freeform notes
  - Submit button → XP toast
- [ ] Weekly review page:
  - Rich auto-generated summary with stats, charts (simple bar charts via Tailwind/CSS)
  - Reflective prompts (3 questions)
  - Submit → XP toast
- [ ] Review history page:
  - List of past reviews by date
  - Expandable to see responses and summaries

#### 7.2 Notifications
- [ ] `OverdueTaskReminder` notification (email channel)
- [ ] `WeeklyReviewReminder` notification (email channel)
- [ ] `LevelUpNotification` (database channel — in-app)
- [ ] `AchievementNotification` (database channel — in-app)
- [ ] `NotificationController` — list (paginated), markAsRead
- [ ] Notification dropdown in topbar:
  - Bell icon with unread count badge
  - Dropdown list of recent notifications
  - "Mark all read" action
- [ ] Scheduler:
  - `SendOverdueReminders` — daily at 9am
  - `SendWeeklyReviewReminder` — Sunday 6pm
  - `UpdateStreaks` — daily at midnight

#### 7.3 Onboarding Flow
- [ ] `OnboardingController` — archetype, areas, firstGoal, complete
- [ ] `OnboardingService`:
  - `seedAreasForArchetype(User $user, string $archetype)` — create preset life areas with stat mappings
  - `completeOnboarding(User $user)` — set onboarding_completed_at, initialize stats
- [ ] Step 1 — Archetype selection:
  - 5 cards: Student, Researcher, Founder, Professional, Creator
  - Brief description of each
  - Select → save to user.archetype
- [ ] Step 2 — Life area confirmation:
  - Show pre-filled areas from archetype
  - Toggle on/off, rename, add custom
  - Show stat mappings per area
- [ ] Step 3 — First goal:
  - Simple form: title, life area, progress type
  - Optional: create first project + 2-3 tasks
- [ ] Step 4 — Welcome:
  - "Your journey begins" screen
  - Show starting stat profile (all zeros, but named)
  - CTA → go to dashboard
- [ ] Wire `EnsureOnboardingComplete` middleware on all authenticated routes except /onboarding/*

#### 7.4 Settings
- [ ] `SettingsController` — show, update
- [ ] Settings page:
  - Name, email
  - Password change
  - Notification preferences (email reminders on/off)
  - (Later: theme, profile customization)

#### 7.5 Polish & Edge Cases
- [ ] Empty states for all list pages:
  - Goals: "Set your first goal to start building your future"
  - Tasks: "Nothing here yet. Add a task to get started"
  - Projects: "Create a project to organize your work"
- [ ] Welcome-back state:
  - If user hasn't logged in for 3+ days, show encouraging dashboard banner
  - "Welcome back! Let's pick up where you left off."
- [ ] Abandoned goal flow:
  - Confirmation modal: "Priorities change — that's growth too."
  - Goal moves to archived view, not deleted
- [ ] Streak grace period:
  - If streak.last_active_date is yesterday-1, set grace_used=true but don't reset
  - If older, reset current_count to 0
- [ ] Loading states:
  - Task completion checkbox: brief spinner/check animation
  - Form submissions: button loading state
- [ ] Mobile responsiveness pass:
  - Sidebar collapses to hamburger
  - Dashboard stacks to single column
  - Task cards remain usable on small screens
- [ ] Favicon + meta tags + page titles

### Checkpoint 7 (MVP Complete)
```
✓ New user can onboard with archetype → areas → first goal
✓ Daily and weekly reviews work with auto-summaries
✓ Email reminders send for overdue tasks and weekly reviews
✓ In-app notifications for level-ups and achievements
✓ Settings page works
✓ All empty states are encouraging, not blank
✓ Welcome-back banner for returning users
✓ Streak grace period prevents unfair resets
✓ Mobile-responsive across all pages
✓ Complete user journey: register → onboard → plan → do → review → grow
```

---

## Phase Summary

| Phase | Name | Key Deliverable | Est. Complexity |
|-------|------|-----------------|-----------------|
| 1 | Scaffolding | Running app with auth + layout | Low |
| 2 | Database & Models | All tables + relationships | Low-Medium |
| 3 | Life Areas & Goals | Area management + goal tracking | Medium |
| 4 | Projects & Tasks | Full task management + recurring | Medium-High |
| 5 | Dashboard | Daily control centre | Medium |
| 6 | Progression | XP, stats, levels, achievements | High |
| 7 | Reviews + Polish | Reviews, onboarding, notifications | High |

---

## Critical Path

The minimum viable demo requires Phases 1–6. Phase 7 completes the MVP.

```
Fastest path to "wow moment":
Phase 1 → 2 → 4 (skip goals UI temporarily) → 6 (XP on task complete)

This gets you to: "I completed a task and gained XP" in minimum time.
Then backfill Phase 3, 5, 7.
```

However, the recommended build order (Phases 1–7 sequential) produces a cleaner, more testable codebase at each step.

---

## Validation Criteria (MVP Done)

The MVP is complete when a user can:

1. Register and complete template-based onboarding
2. Create and manage life areas with stat mappings
3. Create goals with task-based, KPI, or manual progress
4. Create projects under goals
5. Create and complete tasks (standalone or linked)
6. See XP awarded, stats grow, level increase
7. Experience a level-up moment
8. Unlock at least 2 achievements
9. Complete a daily review and weekly review
10. See a meaningful dashboard that answers "what should I do today?"
11. Receive email reminders for overdue tasks
12. Feel motivated, not overwhelmed

---

## Next Step

Use `/sc:implement` to begin Phase 1 — I'll scaffold the Laravel project, install Breeze, configure Tailwind, and build the base layout with all reusable UI components.

Ready to start building?
