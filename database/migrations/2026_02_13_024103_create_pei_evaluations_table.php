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
            $table->foreignId('pei_id')->constrained('peis')->cascadeOnDelete();
            
            // Avaliação e Parecer 
            $table->longText('evaluation_instruments'); 
            $table->longText('final_parecer');        
            $table->longText('successful_proposals');   
            $table->longText('next_stage_goals')->nullable();       
            
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
