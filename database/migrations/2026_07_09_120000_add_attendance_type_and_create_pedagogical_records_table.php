<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->string('attendance_type')
                ->default('aee')
                ->after('type')
                ->index();
        });

        Schema::create('pedagogical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_session_id')
                ->unique()
                ->constrained('attendance_sessions')
                ->cascadeOnDelete();
            $table->string('duration', 50);
            $table->longText('planned_performed_activities');
            $table->longText('pedagogical_record');
            $table->longText('resources_used')->nullable();
            $table->longText('general_observations')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedagogical_records');

        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->dropIndex(['attendance_type']);
            $table->dropColumn('attendance_type');
        });
    }
};
