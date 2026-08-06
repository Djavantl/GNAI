<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedagogical_records', function (Blueprint $table): void {
            $table->renameColumn('pedagogical_record', 'systematic_pedagogical_follow_up_record');
            $table->renameColumn('resources_used', 'strategies_and_resources_adopted');
            $table->renameColumn('general_observations', 'complementary_observations');
        });

        Schema::table('pedagogical_records', function (Blueprint $table): void {
            $table->longText('follow_up_reason')->nullable()->after('attendance_session_id');
            $table->string('follow_up_status')
                ->default('ongoing')
                ->after('follow_up_reason');
            $table->longText('disciplines_with_failure_or_academic_risk')->nullable()
                ->after('strategies_and_resources_adopted');
            $table->longText('school_attendance_status')->nullable()
                ->after('disciplines_with_failure_or_academic_risk');
            $table->longText('referrals_made')->nullable()
                ->after('school_attendance_status');
            $table->dropColumn('planned_performed_activities');
        });
    }

    public function down(): void
    {
        Schema::table('pedagogical_records', function (Blueprint $table): void {
            $table->longText('planned_performed_activities')->nullable()->after('absence_reason');
            $table->dropColumn([
                'follow_up_reason',
                'follow_up_status',
                'disciplines_with_failure_or_academic_risk',
                'school_attendance_status',
                'referrals_made',
            ]);
        });

        Schema::table('pedagogical_records', function (Blueprint $table): void {
            $table->renameColumn('systematic_pedagogical_follow_up_record', 'pedagogical_record');
            $table->renameColumn('strategies_and_resources_adopted', 'resources_used');
            $table->renameColumn('complementary_observations', 'general_observations');
        });
    }
};
