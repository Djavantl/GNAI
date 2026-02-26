<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_courses', function (Blueprint $column) {
            $column->id();
            
            // Chave estrangeira para o Professor
            $column->foreignId('teacher_id')
                ->constrained('teachers')
                ->onDelete('cascade');

            // Chave estrangeira para o Curso
            $column->foreignId('course_id')
                ->constrained('courses')
                ->onDelete('cascade');

            $column->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_courses');
    }
};