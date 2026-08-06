<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_courses', function (Blueprint $table): void {
            $table->longText('school_attendance_status')->nullable()->after('is_current');
        });

        Schema::create('student_course_disciplines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_course_id')->constrained('student_courses')->cascadeOnDelete();
            $table->foreignId('discipline_id')->constrained('disciplines')->restrictOnDelete();
            $table->string('category');
            $table->timestamps();
            $table->unique(
                ['student_course_id', 'discipline_id', 'category'],
                'student_course_discipline_category_unique',
            );
        });

        Schema::dropIfExists('pedagogical_record_disciplines');

        Schema::table('pedagogical_records', function (Blueprint $table): void {
            $table->dropColumn(['follow_up_status', 'school_attendance_status']);
        });

        Schema::table('students', function (Blueprint $table): void {
            $table->dropColumn('is_repeater');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table): void {
            $table->boolean('is_repeater')->nullable()->after('entry_date');
        });

        Schema::table('pedagogical_records', function (Blueprint $table): void {
            $table->string('follow_up_status')->default('ongoing')->after('follow_up_reason');
            $table->longText('school_attendance_status')->nullable()->after('strategies_and_resources_adopted');
        });

        Schema::create('pedagogical_record_disciplines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pedagogical_record_id')->constrained('pedagogical_records')->cascadeOnDelete();
            $table->foreignId('discipline_id')->constrained('disciplines')->restrictOnDelete();
            $table->string('category');
            $table->timestamps();
            $table->unique(
                ['pedagogical_record_id', 'discipline_id', 'category'],
                'ped_record_discipline_category_unique',
            );
        });

        Schema::dropIfExists('student_course_disciplines');
        Schema::table('student_courses', function (Blueprint $table): void {
            $table->dropColumn('school_attendance_status');
        });
    }
};
