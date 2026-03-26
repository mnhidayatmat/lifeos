<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('total_xp')->default(0);
            $table->unsignedInteger('level')->default(1);
            $table->string('rank')->default('initiate');
            $table->string('title')->nullable();
            $table->string('archetype')->nullable();
            $table->timestamp('onboarding_completed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['total_xp', 'level', 'rank', 'title', 'archetype', 'onboarding_completed_at']);
        });
    }
};
