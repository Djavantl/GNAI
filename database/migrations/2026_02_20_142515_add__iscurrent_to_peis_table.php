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
        Schema::table('peis', function (Blueprint $table) {
            $table->integer('version')->default(1)->after('student_context_id');
            $table->boolean('is_current')->default(true)->after('version');

            // opcional: índice para consultas rápidas por aluno/curso/matéria da versão atual
            $table->index(['student_id', 'course_id', 'discipline_id', 'is_current'], 'peis_student_course_discipline_current_idx');

            // opcional (cuide se tiver dados duplicados já existentes)
            // $table->unique(['student_id','course_id','discipline_id','version'], 'peis_unique_version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peis', function (Blueprint $table) {
            $table->dropIndex('peis_student_course_discipline_current_idx');
            // $table->dropUnique('peis_unique_version');
            $table->dropColumn(['version', 'is_current']);
        });
    }
};
