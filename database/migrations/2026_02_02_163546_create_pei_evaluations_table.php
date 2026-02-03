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
        Schema::create('pei_evaluations', function (Blueprint $table) {
            $table->id();
            // Vinculada à adaptação da disciplina específica
            $table->foreignId('pei_adaptation_id')->constrained('pei_adaptations')->cascadeOnDelete();
            
            // Avaliação e Parecer 
            $table->longText('evaluation_instruments'); // Como a aprendizagem foi avaliada 
            $table->longText('final_parecer');       // Descrição de avanços e dificuldades 
            $table->longText('successful_proposals');   // O que teve êxito e o que não teve 
            $table->longText('next_stage_goals');       // Objetivos para a próxima etapa 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pei_evaluations');
    }
};
