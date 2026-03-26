<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('vision_statement')->nullable();
            $table->json('i_am_statements')->nullable(); // ["I am disciplined", "I am a leader"]
            $table->text('anti_vision')->nullable(); // what I don't want to become
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visions');
    }
};
