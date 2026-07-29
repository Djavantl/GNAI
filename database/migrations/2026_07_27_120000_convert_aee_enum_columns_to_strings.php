<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE people MODIFY gender VARCHAR(50) NOT NULL DEFAULT 'not_specified'");

        DB::statement("ALTER TABLE students MODIFY status VARCHAR(50) NOT NULL DEFAULT 'active'");

        DB::statement("ALTER TABLE professionals MODIFY status VARCHAR(50) NOT NULL DEFAULT 'active'");

        DB::statement('ALTER TABLE students_deficiencies MODIFY severity VARCHAR(50) NULL');

        DB::statement('ALTER TABLE student_contexts MODIFY evaluation_type VARCHAR(50) NOT NULL');
        DB::statement('ALTER TABLE student_contexts MODIFY learning_level VARCHAR(50) NULL');
        DB::statement('ALTER TABLE student_contexts MODIFY attention_level VARCHAR(50) NULL');
        DB::statement('ALTER TABLE student_contexts MODIFY memory_level VARCHAR(50) NULL');
        DB::statement('ALTER TABLE student_contexts MODIFY reasoning_level VARCHAR(50) NULL');
        DB::statement('ALTER TABLE student_contexts MODIFY communication_type VARCHAR(50) NULL');
        DB::statement('ALTER TABLE student_contexts MODIFY interaction_level VARCHAR(50) NULL');
        DB::statement('ALTER TABLE student_contexts MODIFY socialization_level VARCHAR(50) NULL');
        DB::statement('ALTER TABLE student_contexts MODIFY autonomy_level VARCHAR(50) NULL');
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE people MODIFY gender ENUM('male', 'female', 'other', 'not_specified') NOT NULL DEFAULT 'not_specified'");

        DB::statement("ALTER TABLE students MODIFY status ENUM('active', 'locked', 'completed', 'dropped') NOT NULL DEFAULT 'active'");

        DB::statement("ALTER TABLE professionals MODIFY status ENUM('active', 'inactive') NOT NULL DEFAULT 'active'");

        DB::statement("ALTER TABLE students_deficiencies MODIFY severity ENUM('mild', 'moderate', 'severe') NULL");

        DB::statement("ALTER TABLE student_contexts MODIFY evaluation_type ENUM('initial', 'periodic_review', 'pei_review', 'specific_demand') NOT NULL");
        DB::statement("ALTER TABLE student_contexts MODIFY learning_level ENUM('very_low', 'low', 'adequate', 'good', 'excellent') NULL");
        DB::statement("ALTER TABLE student_contexts MODIFY attention_level ENUM('very_low', 'low', 'moderate', 'high') NULL");
        DB::statement("ALTER TABLE student_contexts MODIFY memory_level ENUM('low', 'moderate', 'good') NULL");
        DB::statement("ALTER TABLE student_contexts MODIFY reasoning_level ENUM('concrete', 'mixed', 'abstract') NULL");
        DB::statement("ALTER TABLE student_contexts MODIFY communication_type ENUM('verbal', 'non_verbal', 'mixed') NULL");
        DB::statement("ALTER TABLE student_contexts MODIFY interaction_level ENUM('very_low', 'low', 'moderate', 'good') NULL");
        DB::statement("ALTER TABLE student_contexts MODIFY socialization_level ENUM('isolated', 'selective', 'participative') NULL");
        DB::statement("ALTER TABLE student_contexts MODIFY autonomy_level ENUM('dependent', 'partial', 'independent') NULL");
    }
};
