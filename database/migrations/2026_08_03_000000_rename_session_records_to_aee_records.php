<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_session_evaluations', function (Blueprint $table): void {
            $table->dropForeign(['session_record_id']);
            $table->dropUnique('unique_student_evaluation');
        });

        Schema::rename('session_records', 'aee_records');
        Schema::rename('student_session_evaluations', 'aee_student_evaluations');

        Schema::table('aee_records', function (Blueprint $table): void {
            $table->unique('attendance_session_id');
        });

        Schema::table('aee_student_evaluations', function (Blueprint $table): void {
            $table->renameColumn('session_record_id', 'aee_record_id');
        });

        Schema::table('aee_student_evaluations', function (Blueprint $table): void {
            $table->foreign('aee_record_id')->references('id')->on('aee_records')->cascadeOnDelete();
            $table->unique(['aee_record_id', 'student_id'], 'unique_aee_student_evaluation');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('aee_student_evaluations', 'aee_record_id')) {
            Schema::table('aee_student_evaluations', function (Blueprint $table): void {
                $table->dropForeign(['aee_record_id']);
                $table->dropUnique('unique_aee_student_evaluation');
            });

            Schema::table('aee_student_evaluations', function (Blueprint $table): void {
                $table->renameColumn('aee_record_id', 'session_record_id');
            });
        }

        if (! Schema::hasIndex('aee_records', 'aee_records_attendance_session_id_index')) {
            Schema::table('aee_records', function (Blueprint $table): void {
                $table->index('attendance_session_id');
            });
        }

        Schema::table('aee_records', function (Blueprint $table): void {
            $table->dropUnique(['attendance_session_id']);
        });

        Schema::rename('aee_student_evaluations', 'student_session_evaluations');
        Schema::rename('aee_records', 'session_records');

        Schema::table('student_session_evaluations', function (Blueprint $table): void {
            $table->foreign('session_record_id')->references('id')->on('session_records')->cascadeOnDelete();
            $table->unique(['session_record_id', 'student_id'], 'unique_student_evaluation');
        });
    }
};
