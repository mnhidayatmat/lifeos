<?php

namespace Database\Seeders;

use App\Models\Goal;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Seeder;

class ImportTasksSeeder extends Seeder
{
    public function run(): void
    {
        $uid = 1;

        // Life area IDs: 1=Personal, 2=Homestay, 3=Research, 4=Health, 5=Teaching

        // === GOALS ===
        $g1 = Goal::create(['user_id' => $uid, 'life_area_id' => 5, 'title' => 'Deliver excellent teaching this semester', 'status' => 'in_progress', 'priority' => 'high', 'progress_type' => 'task_based']);
        $g2 = Goal::create(['user_id' => $uid, 'life_area_id' => 5, 'title' => 'Improve student engagement and assessment quality', 'status' => 'in_progress', 'priority' => 'medium', 'progress_type' => 'task_based']);

        $g3 = Goal::create(['user_id' => $uid, 'life_area_id' => 3, 'title' => 'Publish research papers and advance academic output', 'status' => 'in_progress', 'priority' => 'high', 'progress_type' => 'task_based', 'is_domino' => true]);
        $g4 = Goal::create(['user_id' => $uid, 'life_area_id' => 3, 'title' => 'Supervise postgraduate students effectively', 'status' => 'in_progress', 'priority' => 'medium', 'progress_type' => 'task_based']);

        $g5 = Goal::create(['user_id' => $uid, 'life_area_id' => 1, 'title' => 'Build LifeOS into a polished productivity platform', 'status' => 'in_progress', 'priority' => 'high', 'progress_type' => 'task_based']);
        $g6 = Goal::create(['user_id' => $uid, 'life_area_id' => 1, 'title' => 'Maintain work-life balance and personal growth', 'status' => 'in_progress', 'priority' => 'medium', 'progress_type' => 'task_based']);

        $g7 = Goal::create(['user_id' => $uid, 'life_area_id' => 2, 'title' => 'Grow Wafa Homestay revenue and guest satisfaction', 'status' => 'in_progress', 'priority' => 'high', 'progress_type' => 'task_based']);

        $g8 = Goal::create(['user_id' => $uid, 'life_area_id' => 4, 'title' => 'Build consistent health and fitness routine', 'status' => 'in_progress', 'priority' => 'medium', 'progress_type' => 'task_based']);

        // === PROJECTS ===
        // Teaching
        $p1 = Project::create(['user_id' => $uid, 'life_area_id' => 5, 'title' => 'Semester Course Delivery', 'status' => 'in_progress', 'priority' => 'high', 'impact_score' => 8, 'confidence_score' => 9, 'ease_score' => 7]);
        $p2 = Project::create(['user_id' => $uid, 'life_area_id' => 5, 'title' => 'Student Assessment & Grading', 'status' => 'in_progress', 'priority' => 'high', 'impact_score' => 8, 'confidence_score' => 8, 'ease_score' => 6]);

        // Research
        $p3 = Project::create(['user_id' => $uid, 'life_area_id' => 3, 'title' => 'Journal Paper Writing', 'status' => 'in_progress', 'priority' => 'high', 'impact_score' => 10, 'confidence_score' => 7, 'ease_score' => 5]);
        $p4 = Project::create(['user_id' => $uid, 'life_area_id' => 3, 'title' => 'Postgrad Supervision', 'status' => 'in_progress', 'priority' => 'medium', 'impact_score' => 8, 'confidence_score' => 8, 'ease_score' => 6]);
        $p5 = Project::create(['user_id' => $uid, 'life_area_id' => 3, 'title' => 'Grant Application', 'status' => 'in_progress', 'priority' => 'high', 'impact_score' => 9, 'confidence_score' => 6, 'ease_score' => 4]);

        // Personal
        $p6 = Project::create(['user_id' => $uid, 'life_area_id' => 1, 'title' => 'LifeOS Development', 'status' => 'in_progress', 'priority' => 'high', 'impact_score' => 9, 'confidence_score' => 8, 'ease_score' => 7]);
        $p7 = Project::create(['user_id' => $uid, 'life_area_id' => 1, 'title' => 'Server & Infrastructure', 'status' => 'in_progress', 'priority' => 'medium', 'impact_score' => 7, 'confidence_score' => 9, 'ease_score' => 8]);

        // Homestay
        $p8 = Project::create(['user_id' => $uid, 'life_area_id' => 2, 'title' => 'Wafa Homestay Operations', 'status' => 'in_progress', 'priority' => 'high', 'impact_score' => 8, 'confidence_score' => 8, 'ease_score' => 7]);
        $p9 = Project::create(['user_id' => $uid, 'life_area_id' => 2, 'title' => 'Homestay Marketing & Bookings', 'status' => 'in_progress', 'priority' => 'medium', 'impact_score' => 8, 'confidence_score' => 7, 'ease_score' => 6]);

        // Health
        $p10 = Project::create(['user_id' => $uid, 'life_area_id' => 4, 'title' => 'Fitness Routine', 'status' => 'in_progress', 'priority' => 'medium', 'impact_score' => 7, 'confidence_score' => 7, 'ease_score' => 8]);

        // === TASKS ===

        // Teaching tasks
        Task::create(['user_id' => $uid, 'goal_id' => $g1->id, 'project_id' => $p1->id, 'title' => 'Prepare lecture slides for Week 10', 'effort' => 'medium', 'priority' => 'high', 'is_important' => true, 'due_date' => '2026-03-27']);
        Task::create(['user_id' => $uid, 'goal_id' => $g1->id, 'project_id' => $p1->id, 'title' => 'Record video tutorial for online students', 'effort' => 'large', 'priority' => 'medium', 'due_date' => '2026-03-28']);
        Task::create(['user_id' => $uid, 'goal_id' => $g1->id, 'project_id' => $p1->id, 'title' => 'Update course outline on LMS', 'effort' => 'small', 'priority' => 'medium', 'due_date' => '2026-03-27']);
        Task::create(['user_id' => $uid, 'goal_id' => $g2->id, 'project_id' => $p2->id, 'title' => 'Design mid-term exam questions', 'effort' => 'large', 'priority' => 'high', 'is_important' => true, 'due_date' => '2026-04-01']);
        Task::create(['user_id' => $uid, 'goal_id' => $g2->id, 'project_id' => $p2->id, 'title' => 'Grade Assignment 2 submissions', 'effort' => 'large', 'priority' => 'high', 'is_important' => true, 'due_date' => '2026-03-30']);
        Task::create(['user_id' => $uid, 'goal_id' => $g2->id, 'project_id' => $p2->id, 'title' => 'Prepare rubric for final project', 'effort' => 'medium', 'priority' => 'medium', 'due_date' => '2026-04-05']);
        Task::create(['user_id' => $uid, 'goal_id' => $g1->id, 'project_id' => $p1->id, 'title' => 'Hold consultation hours for struggling students', 'effort' => 'small', 'priority' => 'medium', 'due_date' => '2026-03-28']);

        // Research tasks
        Task::create(['user_id' => $uid, 'goal_id' => $g3->id, 'project_id' => $p3->id, 'title' => 'Complete literature review section for journal paper', 'effort' => 'large', 'priority' => 'high', 'is_important' => true, 'due_date' => '2026-03-30']);
        Task::create(['user_id' => $uid, 'goal_id' => $g3->id, 'project_id' => $p3->id, 'title' => 'Analyze experiment results and prepare figures', 'effort' => 'large', 'priority' => 'high', 'is_important' => true, 'due_date' => '2026-04-02']);
        Task::create(['user_id' => $uid, 'goal_id' => $g3->id, 'project_id' => $p3->id, 'title' => 'Write methodology section draft', 'effort' => 'large', 'priority' => 'high', 'due_date' => '2026-04-05']);
        Task::create(['user_id' => $uid, 'goal_id' => $g3->id, 'project_id' => $p5->id, 'title' => 'Submit FRGS grant proposal', 'effort' => 'large', 'priority' => 'high', 'is_important' => true, 'due_date' => '2026-04-15']);
        Task::create(['user_id' => $uid, 'goal_id' => $g4->id, 'project_id' => $p4->id, 'title' => 'Review student thesis Chapter 3 draft', 'effort' => 'medium', 'priority' => 'medium', 'due_date' => '2026-03-28']);
        Task::create(['user_id' => $uid, 'goal_id' => $g4->id, 'project_id' => $p4->id, 'title' => 'Meet with PhD student to discuss progress', 'effort' => 'small', 'priority' => 'medium', 'due_date' => '2026-03-27']);
        Task::create(['user_id' => $uid, 'goal_id' => $g3->id, 'project_id' => $p3->id, 'title' => 'Respond to journal reviewer comments', 'effort' => 'medium', 'priority' => 'high', 'is_important' => true, 'due_date' => '2026-04-03']);

        // Personal / LifeOS tasks
        Task::create(['user_id' => $uid, 'goal_id' => $g5->id, 'project_id' => $p6->id, 'title' => 'Deploy LifeOS to production server', 'effort' => 'medium', 'priority' => 'high', 'is_important' => true, 'status' => 'completed', 'completed_at' => now()]);
        Task::create(['user_id' => $uid, 'goal_id' => $g5->id, 'project_id' => $p6->id, 'title' => 'Set up Brevo email integration', 'effort' => 'medium', 'priority' => 'high', 'status' => 'completed', 'completed_at' => now()]);
        Task::create(['user_id' => $uid, 'goal_id' => $g5->id, 'project_id' => $p6->id, 'title' => 'Add weekly review feature with reflection prompts', 'effort' => 'large', 'priority' => 'high', 'due_date' => '2026-03-30']);
        Task::create(['user_id' => $uid, 'goal_id' => $g5->id, 'project_id' => $p6->id, 'title' => 'Build analytics dashboard with charts', 'effort' => 'large', 'priority' => 'medium', 'due_date' => '2026-04-05']);
        Task::create(['user_id' => $uid, 'goal_id' => $g5->id, 'project_id' => $p6->id, 'title' => 'Add dark mode support', 'effort' => 'medium', 'priority' => 'low', 'due_date' => '2026-04-10']);
        Task::create(['user_id' => $uid, 'goal_id' => $g5->id, 'project_id' => $p7->id, 'title' => 'Configure SSL and domain for all services', 'effort' => 'small', 'priority' => 'medium', 'status' => 'completed', 'completed_at' => now()]);
        Task::create(['user_id' => $uid, 'goal_id' => $g5->id, 'project_id' => $p7->id, 'title' => 'Set up automated database backups', 'effort' => 'medium', 'priority' => 'high', 'due_date' => '2026-03-28']);
        Task::create(['user_id' => $uid, 'goal_id' => $g6->id, 'title' => 'Plan weekend family outing', 'effort' => 'small', 'priority' => 'low', 'due_date' => '2026-03-28']);
        Task::create(['user_id' => $uid, 'goal_id' => $g6->id, 'title' => 'Read 30 minutes before sleep', 'effort' => 'small', 'priority' => 'low', 'is_recurring' => true, 'recurrence_rule' => 'daily']);

        // Homestay tasks
        Task::create(['user_id' => $uid, 'goal_id' => $g7->id, 'project_id' => $p8->id, 'title' => 'Confirm weekend guest bookings', 'effort' => 'small', 'priority' => 'high', 'is_important' => true, 'due_date' => '2026-03-27']);
        Task::create(['user_id' => $uid, 'goal_id' => $g7->id, 'project_id' => $p8->id, 'title' => 'Arrange cleaning service for checkout', 'effort' => 'small', 'priority' => 'high', 'due_date' => '2026-03-28']);
        Task::create(['user_id' => $uid, 'goal_id' => $g7->id, 'project_id' => $p8->id, 'title' => 'Restock toiletries and supplies', 'effort' => 'small', 'priority' => 'medium', 'due_date' => '2026-03-29']);
        Task::create(['user_id' => $uid, 'goal_id' => $g7->id, 'project_id' => $p9->id, 'title' => 'Update Airbnb listing photos', 'effort' => 'medium', 'priority' => 'medium', 'due_date' => '2026-04-01']);
        Task::create(['user_id' => $uid, 'goal_id' => $g7->id, 'project_id' => $p9->id, 'title' => 'Respond to guest reviews on Booking.com', 'effort' => 'small', 'priority' => 'medium', 'due_date' => '2026-03-27']);
        Task::create(['user_id' => $uid, 'goal_id' => $g7->id, 'project_id' => $p8->id, 'title' => 'Fix leaking bathroom tap in Unit 2', 'effort' => 'medium', 'priority' => 'high', 'due_date' => '2026-03-29']);
        Task::create(['user_id' => $uid, 'goal_id' => $g7->id, 'project_id' => $p9->id, 'title' => 'Set up Raya holiday special pricing', 'effort' => 'small', 'priority' => 'medium', 'due_date' => '2026-03-30']);

        // Health tasks
        Task::create(['user_id' => $uid, 'goal_id' => $g8->id, 'project_id' => $p10->id, 'title' => 'Morning jog 3x this week', 'effort' => 'medium', 'priority' => 'medium', 'is_recurring' => true, 'recurrence_rule' => 'weekly']);
        Task::create(['user_id' => $uid, 'goal_id' => $g8->id, 'project_id' => $p10->id, 'title' => 'Schedule annual health checkup', 'effort' => 'small', 'priority' => 'low', 'due_date' => '2026-04-15']);
        Task::create(['user_id' => $uid, 'goal_id' => $g8->id, 'title' => 'Drink 2L water daily', 'effort' => 'small', 'priority' => 'low', 'is_recurring' => true, 'recurrence_rule' => 'daily']);
    }
}
