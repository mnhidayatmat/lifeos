<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('habits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('life_area_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('routine')->default('morning'); // morning, afternoon, evening
            $table->string('frequency')->default('daily'); // daily, weekdays, weekends, custom
            $table->json('frequency_days')->nullable(); // e.g. ["mon","wed","fri"]
            $table->string('effort')->default('small'); // small, medium, large
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('habits');
    }
};
