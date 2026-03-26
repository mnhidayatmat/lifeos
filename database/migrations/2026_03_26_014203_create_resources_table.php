<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('life_area_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('type')->default('book'); // book, article, podcast, course, video
            $table->string('author')->nullable();
            $table->string('url')->nullable();
            $table->string('status')->default('to_consume'); // to_consume, in_progress, completed
            $table->text('notes')->nullable();
            $table->unsignedInteger('rating')->nullable(); // 1-5
            $table->unsignedInteger('xp_awarded')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
