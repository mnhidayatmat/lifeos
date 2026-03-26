<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\LifeAreaController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgressionController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SubtaskController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\VisionController;
use App\Http\Middleware\EnsureOnboardingComplete;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Onboarding (auth required, no onboarding check)
Route::middleware('auth')->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');
    Route::post('/onboarding/archetype', [OnboardingController::class, 'storeArchetype'])->name('onboarding.store-archetype');
    Route::get('/onboarding/areas', [OnboardingController::class, 'areas'])->name('onboarding.areas');
    Route::post('/onboarding/areas', [OnboardingController::class, 'storeAreas'])->name('onboarding.store-areas');
    Route::get('/onboarding/first-goal', [OnboardingController::class, 'firstGoal'])->name('onboarding.first-goal');
    Route::post('/onboarding/first-goal', [OnboardingController::class, 'storeFirstGoal'])->name('onboarding.store-first-goal');
    Route::get('/onboarding/welcome', [OnboardingController::class, 'welcome'])->name('onboarding.welcome');
});

// Main app (auth + onboarding required)
Route::middleware(['auth', 'verified', EnsureOnboardingComplete::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Life Areas
    Route::get('/life-areas', [LifeAreaController::class, 'index'])->name('life-areas.index');
    Route::post('/life-areas', [LifeAreaController::class, 'store'])->name('life-areas.store');
    Route::put('/life-areas/{lifeArea}', [LifeAreaController::class, 'update'])->name('life-areas.update');
    Route::delete('/life-areas/{lifeArea}', [LifeAreaController::class, 'destroy'])->name('life-areas.destroy');
    Route::patch('/life-areas/{lifeArea}/toggle', [LifeAreaController::class, 'toggle'])->name('life-areas.toggle');
    Route::post('/life-areas/reorder', [LifeAreaController::class, 'reorder'])->name('life-areas.reorder');

    // Goals
    Route::get('/goals', [GoalController::class, 'index'])->name('goals.index');
    Route::get('/goals/create', [GoalController::class, 'create'])->name('goals.create');
    Route::post('/goals', [GoalController::class, 'store'])->name('goals.store');
    Route::get('/goals/{goal}', [GoalController::class, 'show'])->name('goals.show');
    Route::get('/goals/{goal}/edit', [GoalController::class, 'edit'])->name('goals.edit');
    Route::put('/goals/{goal}', [GoalController::class, 'update'])->name('goals.update');
    Route::delete('/goals/{goal}', [GoalController::class, 'destroy'])->name('goals.destroy');
    Route::patch('/goals/{goal}/status', [GoalController::class, 'updateStatus'])->name('goals.update-status');
    Route::patch('/goals/{goal}/progress', [GoalController::class, 'updateProgress'])->name('goals.update-progress');
    Route::patch('/goals/{goal}/domino', [GoalController::class, 'toggleDomino'])->name('goals.toggle-domino');

    // Projects
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::patch('/projects/{project}/status', [ProjectController::class, 'updateStatus'])->name('projects.update-status');

    // Tasks
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
    Route::patch('/tasks/{task}/reopen', [TaskController::class, 'reopen'])->name('tasks.reopen');

    // Subtasks
    Route::post('/tasks/{task}/subtasks', [SubtaskController::class, 'store'])->name('subtasks.store');
    Route::patch('/subtasks/{subtask}/toggle', [SubtaskController::class, 'toggle'])->name('subtasks.toggle');
    Route::delete('/subtasks/{subtask}', [SubtaskController::class, 'destroy'])->name('subtasks.destroy');

    // Habits
    Route::get('/habits', [HabitController::class, 'index'])->name('habits.index');
    Route::post('/habits', [HabitController::class, 'store'])->name('habits.store');
    Route::put('/habits/{habit}', [HabitController::class, 'update'])->name('habits.update');
    Route::delete('/habits/{habit}', [HabitController::class, 'destroy'])->name('habits.destroy');
    Route::patch('/habits/{habit}/toggle', [HabitController::class, 'toggle'])->name('habits.toggle');

    // Reviews
    Route::get('/reviews/daily', [ReviewController::class, 'daily'])->name('reviews.daily');
    Route::post('/reviews/daily', [ReviewController::class, 'submitDaily'])->name('reviews.submit-daily');
    Route::get('/reviews/weekly', [ReviewController::class, 'weekly'])->name('reviews.weekly');
    Route::post('/reviews/weekly', [ReviewController::class, 'submitWeekly'])->name('reviews.submit-weekly');
    Route::get('/reviews/monthly', [ReviewController::class, 'monthly'])->name('reviews.monthly');
    Route::post('/reviews/monthly', [ReviewController::class, 'submitMonthly'])->name('reviews.submit-monthly');
    Route::get('/reviews/history', [ReviewController::class, 'history'])->name('reviews.history');

    // Vision & Identity
    Route::get('/vision', [VisionController::class, 'index'])->name('vision.index');
    Route::post('/vision/statement', [VisionController::class, 'updateVision'])->name('vision.update-statement');
    Route::post('/vision/i-am', [VisionController::class, 'updateStatements'])->name('vision.update-iam');
    Route::post('/vision/traits', [VisionController::class, 'storeTrait'])->name('vision.store-trait');
    Route::patch('/vision/traits/{identityTrait}', [VisionController::class, 'updateTrait'])->name('vision.update-trait');
    Route::delete('/vision/traits/{identityTrait}', [VisionController::class, 'destroyTrait'])->name('vision.destroy-trait');

    // Knowledge Library
    Route::get('/resources', [ResourceController::class, 'index'])->name('resources.index');
    Route::post('/resources', [ResourceController::class, 'store'])->name('resources.store');
    Route::put('/resources/{resource}', [ResourceController::class, 'update'])->name('resources.update');
    Route::delete('/resources/{resource}', [ResourceController::class, 'destroy'])->name('resources.destroy');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // Progression
    Route::get('/progression', [ProgressionController::class, 'index'])->name('progression.index');
    Route::get('/progression/achievements', [ProgressionController::class, 'achievements'])->name('progression.achievements');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

require __DIR__.'/auth.php';
