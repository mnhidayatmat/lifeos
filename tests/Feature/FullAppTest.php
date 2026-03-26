<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Goal;
use App\Models\LifeArea;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\OnboardingService;
use App\Services\XpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FullAppTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $freshUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed achievements
        $this->artisan('db:seed', ['--class' => 'AchievementSeeder']);

        // Create admin (fully onboarded)
        $this->admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@lifeos.app',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $onboarding = app(OnboardingService::class);
        $this->admin->update(['archetype' => 'professional']);
        $onboarding->seedAreasForArchetype($this->admin, 'professional');
        $onboarding->completeOnboarding($this->admin);

        // Create first goal for admin
        $area = $this->admin->lifeAreas()->first();
        $this->admin->goals()->create([
            'title' => 'Ship MVP',
            'life_area_id' => $area->id,
            'progress_type' => 'task_based',
            'status' => 'in_progress',
            'priority' => 'high',
        ]);

        // Create a fresh (non-onboarded) user
        $this->freshUser = User::create([
            'name' => 'Fresh User',
            'email' => 'fresh@lifeos.app',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);
    }

    // ===== GUEST ROUTES =====

    public function test_welcome_page(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_login_page(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_register_page(): void
    {
        $this->get('/register')->assertStatus(200);
    }

    public function test_unauthenticated_redirected(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/goals')->assertRedirect('/login');
        $this->get('/tasks')->assertRedirect('/login');
    }

    // ===== ONBOARDING =====

    public function test_non_onboarded_user_redirected_to_onboarding(): void
    {
        $this->actingAs($this->freshUser)
            ->get('/dashboard')
            ->assertRedirect('/onboarding');
    }

    public function test_onboarding_page_loads(): void
    {
        $this->actingAs($this->freshUser)
            ->get('/onboarding')
            ->assertStatus(200);
    }

    public function test_onboarding_full_flow(): void
    {
        // Step 1: Choose archetype
        $this->actingAs($this->freshUser)
            ->post('/onboarding/archetype', ['archetype' => 'student'])
            ->assertRedirect('/onboarding/areas');

        $this->freshUser->refresh();
        $this->assertEquals('student', $this->freshUser->archetype);
        $this->assertGreaterThan(0, $this->freshUser->lifeAreas()->count());

        // Step 2: Confirm areas
        $this->actingAs($this->freshUser)
            ->get('/onboarding/areas')
            ->assertStatus(200);

        $this->actingAs($this->freshUser)
            ->post('/onboarding/areas')
            ->assertRedirect('/onboarding/first-goal');

        // Step 3: Create first goal
        $area = $this->freshUser->lifeAreas()->first();
        $this->actingAs($this->freshUser)
            ->post('/onboarding/first-goal', [
                'title' => 'Ace my exams',
                'life_area_id' => $area->id,
            ])
            ->assertRedirect('/onboarding/welcome');

        $this->freshUser->refresh();
        $this->assertTrue($this->freshUser->hasCompletedOnboarding());
        $this->assertEquals(8, $this->freshUser->stats()->count());

        // Step 4: Welcome page
        $this->actingAs($this->freshUser)
            ->get('/onboarding/welcome')
            ->assertStatus(200);

        // Now dashboard should work
        $this->actingAs($this->freshUser)
            ->get('/dashboard')
            ->assertStatus(200);
    }

    // ===== DASHBOARD =====

    public function test_dashboard_loads(): void
    {
        $this->actingAs($this->admin)
            ->get('/dashboard')
            ->assertStatus(200)
            ->assertSee('Dashboard')
            ->assertSee('Today', false);
    }

    // ===== LIFE AREAS =====

    public function test_life_areas_index(): void
    {
        $this->actingAs($this->admin)
            ->get('/life-areas')
            ->assertStatus(200)
            ->assertSee('Life Areas');
    }

    public function test_create_life_area(): void
    {
        $this->actingAs($this->admin)
            ->post('/life-areas', [
                'name' => 'Hobbies',
                'color' => '#ff6600',
                'primary_stat' => 'creativity',
                'secondary_stat' => 'wisdom',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('life_areas', [
            'user_id' => $this->admin->id,
            'name' => 'Hobbies',
        ]);
    }

    public function test_life_area_cap_enforced(): void
    {
        // Admin already has 5 preset areas + potentially 1 from test above
        // Fill up to 10
        for ($i = 0; $i < 5; $i++) {
            $this->admin->lifeAreas()->create([
                'name' => "Extra Area $i",
                'slug' => "extra-area-$i",
                'color' => '#000000',
                'primary_stat' => 'focus',
                'secondary_stat' => 'discipline',
            ]);
        }

        $this->actingAs($this->admin)
            ->post('/life-areas', [
                'name' => 'Over Limit',
                'color' => '#ff0000',
                'primary_stat' => 'focus',
                'secondary_stat' => 'discipline',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('name');
    }

    public function test_toggle_life_area(): void
    {
        $area = $this->admin->lifeAreas()->where('is_active', true)->first();

        $this->actingAs($this->admin)
            ->patch("/life-areas/{$area->id}/toggle")
            ->assertRedirect();

        $area->refresh();
        $this->assertFalse($area->is_active);
    }

    public function test_update_life_area(): void
    {
        $area = $this->admin->lifeAreas()->first();

        $this->actingAs($this->admin)
            ->put("/life-areas/{$area->id}", [
                'name' => 'Renamed Area',
                'color' => '#123456',
                'primary_stat' => 'wealth',
                'secondary_stat' => 'influence',
            ])
            ->assertRedirect();

        $area->refresh();
        $this->assertEquals('Renamed Area', $area->name);
    }

    public function test_delete_life_area(): void
    {
        $area = $this->admin->lifeAreas()->create([
            'name' => 'Temp Area',
            'slug' => 'temp-area',
            'color' => '#000',
            'primary_stat' => 'focus',
            'secondary_stat' => 'discipline',
        ]);

        $this->actingAs($this->admin)
            ->delete("/life-areas/{$area->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('life_areas', ['id' => $area->id]);
    }

    // ===== GOALS =====

    public function test_goals_index(): void
    {
        $this->actingAs($this->admin)
            ->get('/goals')
            ->assertStatus(200)
            ->assertSee('Ship MVP');
    }

    public function test_goals_filter_by_area(): void
    {
        $area = $this->admin->lifeAreas()->first();

        $this->actingAs($this->admin)
            ->get("/goals?area={$area->id}")
            ->assertStatus(200);
    }

    public function test_create_goal(): void
    {
        $area = $this->admin->lifeAreas()->first();

        $this->actingAs($this->admin)
            ->post('/goals', [
                'title' => 'Learn Laravel Testing',
                'life_area_id' => $area->id,
                'progress_type' => 'kpi_based',
                'target_value' => 10,
                'priority' => 'medium',
            ])
            ->assertRedirect('/goals');

        $this->assertDatabaseHas('goals', ['title' => 'Learn Laravel Testing']);
    }

    public function test_goal_show(): void
    {
        $goal = $this->admin->goals()->first();

        $this->actingAs($this->admin)
            ->get("/goals/{$goal->id}")
            ->assertStatus(200)
            ->assertSee($goal->title);
    }

    public function test_goal_edit(): void
    {
        $goal = $this->admin->goals()->first();

        $this->actingAs($this->admin)
            ->get("/goals/{$goal->id}/edit")
            ->assertStatus(200);
    }

    public function test_goal_update(): void
    {
        $goal = $this->admin->goals()->first();
        $area = $this->admin->lifeAreas()->first();

        $this->actingAs($this->admin)
            ->put("/goals/{$goal->id}", [
                'title' => 'Updated Goal',
                'life_area_id' => $area->id,
                'progress_type' => 'task_based',
                'priority' => 'urgent',
            ])
            ->assertRedirect();

        $goal->refresh();
        $this->assertEquals('Updated Goal', $goal->title);
    }

    public function test_goal_status_update(): void
    {
        $goal = $this->admin->goals()->first();

        $this->actingAs($this->admin)
            ->patch("/goals/{$goal->id}/status", ['status' => 'completed'])
            ->assertRedirect();

        $goal->refresh();
        $this->assertEquals('completed', $goal->status);
        $this->assertNotNull($goal->completed_at);
    }

    public function test_goal_progress_kpi(): void
    {
        $area = $this->admin->lifeAreas()->first();
        $goal = $this->admin->goals()->create([
            'title' => 'KPI Goal',
            'life_area_id' => $area->id,
            'progress_type' => 'kpi_based',
            'target_value' => 10,
            'current_value' => 0,
            'priority' => 'medium',
        ]);

        $this->actingAs($this->admin)
            ->patch("/goals/{$goal->id}/progress", ['current_value' => 5])
            ->assertRedirect();

        $goal->refresh();
        $this->assertEquals(5, $goal->current_value);
        $this->assertEquals(50, $goal->progress);
    }

    public function test_goal_abandoned(): void
    {
        $goal = $this->admin->goals()->first();

        $this->actingAs($this->admin)
            ->patch("/goals/{$goal->id}/status", ['status' => 'abandoned'])
            ->assertRedirect()
            ->assertSessionHas('success');
    }

    // ===== PROJECTS =====

    public function test_projects_index(): void
    {
        $this->actingAs($this->admin)
            ->get('/projects')
            ->assertStatus(200);
    }

    public function test_create_project(): void
    {
        $area = $this->admin->lifeAreas()->first();
        $goal = $this->admin->goals()->first();

        $this->actingAs($this->admin)
            ->post('/projects', [
                'title' => 'Test Project',
                'life_area_id' => $area->id,
                'goal_id' => $goal->id,
                'priority' => 'high',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('projects', ['title' => 'Test Project']);
    }

    public function test_project_show(): void
    {
        $area = $this->admin->lifeAreas()->first();
        $project = $this->admin->projects()->create([
            'title' => 'Show Project',
            'life_area_id' => $area->id,
            'priority' => 'medium',
        ]);

        $this->actingAs($this->admin)
            ->get("/projects/{$project->id}")
            ->assertStatus(200)
            ->assertSee('Show Project');
    }

    public function test_project_status_update(): void
    {
        $area = $this->admin->lifeAreas()->first();
        $project = $this->admin->projects()->create([
            'title' => 'Status Project',
            'life_area_id' => $area->id,
            'priority' => 'medium',
        ]);

        $this->actingAs($this->admin)
            ->patch("/projects/{$project->id}/status", ['status' => 'in_progress'])
            ->assertRedirect();

        $project->refresh();
        $this->assertEquals('in_progress', $project->status);
    }

    // ===== TASKS =====

    public function test_tasks_index_views(): void
    {
        foreach (['today', 'overdue', 'all'] as $view) {
            $this->actingAs($this->admin)
                ->get("/tasks?view=$view")
                ->assertStatus(200);
        }
    }

    public function test_create_standalone_task(): void
    {
        $this->actingAs($this->admin)
            ->post('/tasks', [
                'title' => 'Buy groceries',
                'effort' => 'small',
                'due_date' => today()->format('Y-m-d'),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('tasks', [
            'title' => 'Buy groceries',
            'effort' => 'small',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_create_goal_linked_task(): void
    {
        $goal = $this->admin->goals()->first();

        $this->actingAs($this->admin)
            ->post('/tasks', [
                'title' => 'Write tests',
                'effort' => 'large',
                'goal_id' => $goal->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('tasks', [
            'title' => 'Write tests',
            'goal_id' => $goal->id,
        ]);
    }

    public function test_task_show(): void
    {
        $task = $this->admin->tasks()->create([
            'title' => 'Show Task',
            'effort' => 'medium',
        ]);

        $this->actingAs($this->admin)
            ->get("/tasks/{$task->id}")
            ->assertStatus(200)
            ->assertSee('Show Task');
    }

    public function test_task_complete_awards_xp(): void
    {
        $goal = $this->admin->goals()->first();
        $task = $this->admin->tasks()->create([
            'title' => 'XP Task',
            'effort' => 'large',
            'goal_id' => $goal->id,
        ]);

        $xpBefore = $this->admin->total_xp;

        $this->actingAs($this->admin)
            ->patch("/tasks/{$task->id}/complete")
            ->assertRedirect();

        $task->refresh();
        $this->admin->refresh();

        $this->assertEquals('completed', $task->status);
        $this->assertGreaterThan(0, $task->xp_awarded);
        $this->assertGreaterThan($xpBefore, $this->admin->total_xp);

        // Goal-linked large = 30 * 1.2 = 36 XP
        $this->assertEquals(36, $task->xp_awarded);
    }

    public function test_task_complete_standalone_xp(): void
    {
        $task = $this->admin->tasks()->create([
            'title' => 'Standalone XP',
            'effort' => 'medium',
        ]);

        $this->actingAs($this->admin)
            ->patch("/tasks/{$task->id}/complete")
            ->assertRedirect();

        $task->refresh();
        // Standalone medium = 15 * 1.0 = 15 XP
        $this->assertEquals(15, $task->xp_awarded);
    }

    public function test_task_reopen(): void
    {
        $task = $this->admin->tasks()->create([
            'title' => 'Reopen Task',
            'effort' => 'small',
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->patch("/tasks/{$task->id}/reopen")
            ->assertRedirect();

        $task->refresh();
        $this->assertEquals('pending', $task->status);
        $this->assertNull($task->completed_at);
    }

    public function test_task_reopen_revokes_xp(): void
    {
        $goal = $this->admin->goals()->first();
        $task = $this->admin->tasks()->create([
            'title' => 'Revoke XP Task',
            'effort' => 'large',
            'goal_id' => $goal->id,
        ]);

        // Complete — should award 36 XP
        $this->actingAs($this->admin)
            ->patch("/tasks/{$task->id}/complete");

        $this->admin->refresh();
        $xpAfterComplete = $this->admin->total_xp;
        $task->refresh();
        $this->assertEquals(36, $task->xp_awarded);

        // Reopen — should revoke 36 XP
        $this->actingAs($this->admin)
            ->patch("/tasks/{$task->id}/reopen");

        $this->admin->refresh();
        $task->refresh();
        $this->assertEquals(0, $task->xp_awarded);
        $this->assertEquals($xpAfterComplete - 36, $this->admin->total_xp);
        $this->assertEquals('pending', $task->status);
    }

    public function test_task_complete_json(): void
    {
        $task = $this->admin->tasks()->create([
            'title' => 'JSON Task',
            'effort' => 'small',
        ]);

        $this->actingAs($this->admin)
            ->patchJson("/tasks/{$task->id}/complete")
            ->assertOk()
            ->assertJson(['success' => true]);
    }

    // ===== EISENHOWER MATRIX =====

    public function test_matrix_view_loads(): void
    {
        $this->actingAs($this->admin)
            ->get('/tasks?view=matrix')
            ->assertStatus(200)
            ->assertSee('Do First')
            ->assertSee('Schedule')
            ->assertSee('Delegate')
            ->assertSee('Eliminate');
    }

    public function test_create_important_task(): void
    {
        $this->actingAs($this->admin)
            ->post('/tasks', [
                'title' => 'Important Task',
                'effort' => 'large',
                'priority' => 'urgent',
                'is_important' => '1',
            ])
            ->assertRedirect();

        $task = $this->admin->tasks()->where('title', 'Important Task')->first();
        $this->assertTrue($task->is_important);
        $this->assertEquals('do_first', $task->eisenhowerQuadrant());
    }

    public function test_eisenhower_quadrant_logic(): void
    {
        // Q1: Do First (urgent + important)
        $q1 = $this->admin->tasks()->create(['title' => 'Q1', 'effort' => 'medium', 'priority' => 'urgent', 'is_important' => true]);
        $this->assertEquals('do_first', $q1->eisenhowerQuadrant());

        // Q2: Schedule (not urgent + important)
        $q2 = $this->admin->tasks()->create(['title' => 'Q2', 'effort' => 'medium', 'priority' => 'low', 'is_important' => true]);
        $this->assertEquals('schedule', $q2->eisenhowerQuadrant());

        // Q3: Delegate (urgent + not important)
        $q3 = $this->admin->tasks()->create(['title' => 'Q3', 'effort' => 'medium', 'priority' => 'high', 'is_important' => false]);
        $this->assertEquals('delegate', $q3->eisenhowerQuadrant());

        // Q4: Eliminate (not urgent + not important)
        $q4 = $this->admin->tasks()->create(['title' => 'Q4', 'effort' => 'medium', 'priority' => 'low', 'is_important' => false]);
        $this->assertEquals('eliminate', $q4->eisenhowerQuadrant());
    }

    public function test_matrix_shows_tasks_in_correct_quadrants(): void
    {
        $this->admin->tasks()->create(['title' => 'Urgent Important', 'effort' => 'medium', 'priority' => 'urgent', 'is_important' => true]);
        $this->admin->tasks()->create(['title' => 'Low Not Important', 'effort' => 'small', 'priority' => 'low', 'is_important' => false]);

        $this->actingAs($this->admin)
            ->get('/tasks?view=matrix')
            ->assertSee('Urgent Important')
            ->assertSee('Low Not Important');
    }

    // ===== SUBTASKS =====

    public function test_subtask_crud(): void
    {
        $task = $this->admin->tasks()->create([
            'title' => 'Parent Task',
            'effort' => 'medium',
        ]);

        // Create
        $this->actingAs($this->admin)
            ->post("/tasks/{$task->id}/subtasks", ['title' => 'Sub 1'])
            ->assertRedirect();

        $subtask = $task->subtasks()->first();
        $this->assertNotNull($subtask);
        $this->assertFalse($subtask->is_completed);

        // Toggle
        $this->actingAs($this->admin)
            ->patch("/subtasks/{$subtask->id}/toggle")
            ->assertRedirect();

        $subtask->refresh();
        $this->assertTrue($subtask->is_completed);

        // Delete
        $this->actingAs($this->admin)
            ->delete("/subtasks/{$subtask->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('subtasks', ['id' => $subtask->id]);
    }

    // ===== REVIEWS =====

    public function test_daily_review_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/reviews/daily')
            ->assertStatus(200);
    }

    public function test_submit_daily_review(): void
    {
        $this->actingAs($this->admin)
            ->post('/reviews/daily', [
                'completed_today' => 'Tested the app',
                'mattered_most' => 'Quality',
                'focus_tomorrow' => 'More features',
                'momentum' => 4,
            ])
            ->assertRedirect('/reviews/daily');

        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->admin->id,
            'type' => 'daily',
        ]);
    }

    public function test_weekly_review_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/reviews/weekly')
            ->assertStatus(200);
    }

    public function test_submit_weekly_review(): void
    {
        $this->actingAs($this->admin)
            ->post('/reviews/weekly', [
                'went_well' => 'Everything',
                'got_stuck' => 'Nothing',
                'focus_next_week' => 'Ship it',
            ])
            ->assertRedirect('/reviews/weekly');

        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->admin->id,
            'type' => 'weekly',
        ]);
    }

    public function test_review_history(): void
    {
        $this->actingAs($this->admin)
            ->get('/reviews/history')
            ->assertStatus(200);
    }

    // ===== PROGRESSION =====

    public function test_progression_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/progression')
            ->assertStatus(200)
            ->assertSee('Character Stats');
    }

    public function test_achievements_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/progression/achievements')
            ->assertStatus(200)
            ->assertSee('First Step');
    }

    // ===== NOTIFICATIONS =====

    public function test_notifications_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/notifications')
            ->assertStatus(200);
    }

    // ===== PROFILE =====

    public function test_profile_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/profile')
            ->assertStatus(200);
    }

    // ===== XP FORMULAS =====

    public function test_xp_level_calculation(): void
    {
        $this->assertEquals(1, XpService::calculateLevel(0));
        $this->assertEquals(2, XpService::calculateLevel(25));
        $this->assertEquals(3, XpService::calculateLevel(100));
        $this->assertEquals(5, XpService::calculateLevel(400));
    }

    public function test_xp_for_level(): void
    {
        $this->assertEquals(0, XpService::xpForLevel(1));
        $this->assertEquals(25, XpService::xpForLevel(2));
        $this->assertEquals(100, XpService::xpForLevel(3));
    }

    // ===== AUTHORIZATION =====

    public function test_cannot_access_other_users_goal(): void
    {
        // Onboard fresh user
        $onboarding = app(OnboardingService::class);
        $this->freshUser->update(['archetype' => 'student']);
        $onboarding->seedAreasForArchetype($this->freshUser, 'student');
        $onboarding->completeOnboarding($this->freshUser);

        $adminGoal = $this->admin->goals()->first();

        $this->actingAs($this->freshUser)
            ->get("/goals/{$adminGoal->id}")
            ->assertStatus(403);
    }

    public function test_cannot_complete_other_users_task(): void
    {
        $onboarding = app(OnboardingService::class);
        if (!$this->freshUser->hasCompletedOnboarding()) {
            $this->freshUser->update(['archetype' => 'student']);
            $onboarding->seedAreasForArchetype($this->freshUser, 'student');
            $onboarding->completeOnboarding($this->freshUser);
        }

        $task = $this->admin->tasks()->create([
            'title' => 'Admin Only Task',
            'effort' => 'small',
        ]);

        $this->actingAs($this->freshUser)
            ->patch("/tasks/{$task->id}/complete")
            ->assertStatus(403);
    }

    public function test_cannot_delete_other_users_life_area(): void
    {
        $onboarding = app(OnboardingService::class);
        if (!$this->freshUser->hasCompletedOnboarding()) {
            $this->freshUser->update(['archetype' => 'student']);
            $onboarding->seedAreasForArchetype($this->freshUser, 'student');
            $onboarding->completeOnboarding($this->freshUser);
        }

        $area = $this->admin->lifeAreas()->first();

        $this->actingAs($this->freshUser)
            ->delete("/life-areas/{$area->id}")
            ->assertStatus(403);
    }

    // ===== ACHIEVEMENT SYSTEM =====

    public function test_first_task_achievement_unlocks(): void
    {
        $task = $this->admin->tasks()->create([
            'title' => 'Achievement Trigger',
            'effort' => 'small',
        ]);

        $this->actingAs($this->admin)
            ->patch("/tasks/{$task->id}/complete");

        $this->admin->refresh();
        $firstTaskAchievement = Achievement::where('key', 'first_task')->first();

        $this->assertTrue(
            $this->admin->achievements()
                ->where('achievement_id', $firstTaskAchievement->id)
                ->exists()
        );
    }
}
