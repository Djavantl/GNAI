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
        Schema::create('peis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('professional_id')->constrained('professionals')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete(); 
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('discipline_id')->constrained('disciplines')->cascadeOnDelete();

            $table->string('teacher_name'); 

            $table->foreignId('student_context_id')->constrained('student_contexts')->cascadeOnDelete();
            
            $table->boolean('is_finished')->default(false); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peis');
    }
};
