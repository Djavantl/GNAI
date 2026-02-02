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
        // Contexto educacional do aluno
        Schema::create('student_contexts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('semester_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('evaluated_by_professional_id')
                ->nullable()
                ->constrained('professionals')
                ->nullOnDelete();

            $table->enum('evaluation_type', [
                'initial',
                'periodic_review',
                'pei_review',
                'specific_demand'
            ]);

            $table->boolean('is_current')->default(false);


            // Aprendizagem e cognição

            $table->enum('learning_level', [
                'very_low',
                'low',
                'adequate',
                'good',
                'excellent'
            ])->nullable();

            $table->enum('attention_level', [
                'very_low',
                'low',
                'moderate',
                'high'
            ])->nullable();

            $table->enum('memory_level', [
                'low',
                'moderate',
                'good'
            ])->nullable();

            $table->enum('reasoning_level', [
                'concrete',
                'mixed',
                'abstract'
            ])->nullable();

            $table->text('learning_observations')->nullable();

            // Comunicação, interação e comportamento

            $table->enum('communication_type', [
                'verbal',
                'non_verbal',
                'mixed'
            ])->nullable();

            $table->enum('interaction_level', [
                'very_low',
                'low',
                'moderate',
                'good'
            ])->nullable();

            $table->enum('socialization_level', [
                'isolated',
                'selective',
                'participative'
            ])->nullable();

            $table->boolean('shows_aggressive_behavior')->default(false);
            $table->boolean('shows_withdrawn_behavior')->default(false);

            $table->text('behavior_notes')->nullable();

            // Autonomia, acessibilidade e apoio

            $table->enum('autonomy_level', [
                'dependent',
                'partial',
                'independent'
            ])->nullable();

            $table->boolean('needs_mobility_support')->default(false);
            $table->boolean('needs_communication_support')->default(false);
            $table->boolean('needs_pedagogical_adaptation')->default(false);

            $table->boolean('uses_assistive_technology')->default(false);

            // Saude e acompanhamento

            $table->boolean('has_medical_report')->default(false);
            $table->boolean('uses_medication')->default(false);

            $table->text('medical_notes')->nullable();

            // observações gerais

            $table->text('strengths')->nullable();      
            $table->text('difficulties')->nullable();       
            $table->text('recommendations')->nullable();    

            $table->text('general_observation')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_contexts');
    }
};
