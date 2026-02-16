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
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete(); 
            $table->longText('parecer');        
            $table->longText('successful_proposals');   
            $table->longText('next_stage_goals')->nullable();       
            $table->foreignId('evaluated_by_professional_id')
                ->constrained('professionals')
                ->cascadeOnDelete();

            $table->string('evaluation_type');
            $table->date('evaluation_date');
            
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
