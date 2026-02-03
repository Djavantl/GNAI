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
        Schema::create('pei_adaptations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pei_id')->constrained()->cascadeOnDelete();
            
            // Identificação da Disciplina 
            $table->string('course_subject'); // Componente Curricular 
            $table->string('teacher_name');   // Nome do Docente 

            // Planejamento Pedagógico
            $table->longText('specific_objectives');    // Objetivos específicos para o aluno 
            $table->longText('content_programmatic');   // Conteúdos priorizados ou substituídos 
            $table->longText('methodology_strategies'); // Estratégias e recursos didáticos 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pei_adaptations');
    }
};
