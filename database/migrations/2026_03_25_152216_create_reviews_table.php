<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->date('period_date');
            $table->json('responses')->nullable();
            $table->json('auto_summary')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('xp_awarded')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'type', 'period_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
