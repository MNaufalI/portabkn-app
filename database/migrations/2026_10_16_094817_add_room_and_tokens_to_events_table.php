<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('youtube_url')->nullable()->after('description');
            $table->string('material_path')->nullable()->after('youtube_url');
            $table->string('start_token', 50)->nullable()->after('live_token');
            $table->string('end_token', 50)->nullable()->after('start_token');
            $table->unsignedInteger('attendance_threshold_minutes')->default(30)->after('end_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'youtube_url',
                'material_path',
                'start_token',
                'end_token',
                'attendance_threshold_minutes',
            ]);
        });
    }
};
