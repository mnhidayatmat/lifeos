<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedTinyInteger('impact_score')->nullable()->after('priority');
            $table->unsignedTinyInteger('confidence_score')->nullable()->after('impact_score');
            $table->unsignedTinyInteger('ease_score')->nullable()->after('confidence_score');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['impact_score', 'confidence_score', 'ease_score']);
        });
    }
};
