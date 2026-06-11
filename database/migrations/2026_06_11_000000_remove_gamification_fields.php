<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove the RPG/XP progression layer: experience points, levels, ranks,
     * the 8 character stats, and per-record XP rewards. The app now tracks
     * real productivity metrics instead. Consistency streaks and milestones
     * (formerly achievements) are kept.
     */
    public function up(): void
    {
        Schema::dropIfExists('xp_logs');
        Schema::dropIfExists('user_stats');

        $this->dropColumns('users', ['total_xp', 'level', 'rank', 'title']);
        $this->dropColumns('life_areas', ['primary_stat', 'secondary_stat']);
        $this->dropColumns('tasks', ['xp_awarded']);
        $this->dropColumns('reviews', ['xp_awarded']);
        $this->dropColumns('resources', ['xp_awarded']);
        $this->dropColumns('habit_logs', ['xp_awarded']);
        $this->dropColumns('identity_traits', ['linked_stat']);
        $this->dropColumns('achievements', ['xp_reward']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('total_xp')->default(0);
            $table->unsignedInteger('level')->default(1);
            $table->string('rank')->default('initiate');
            $table->string('title')->nullable();
        });

        Schema::table('life_areas', function (Blueprint $table) {
            $table->string('primary_stat')->nullable();
            $table->string('secondary_stat')->nullable();
        });

        foreach (['tasks', 'reviews', 'resources', 'habit_logs'] as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->unsignedInteger('xp_awarded')->default(0);
            });
        }

        Schema::table('identity_traits', function (Blueprint $table) {
            $table->string('linked_stat')->nullable();
        });

        Schema::table('achievements', function (Blueprint $table) {
            $table->unsignedInteger('xp_reward')->default(0);
        });
    }

    private function dropColumns(string $table, array $columns): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $existing = array_filter($columns, fn ($c) => Schema::hasColumn($table, $c));

        if (! empty($existing)) {
            Schema::table($table, function (Blueprint $table) use ($existing) {
                $table->dropColumn(array_values($existing));
            });
        }
    }
};
