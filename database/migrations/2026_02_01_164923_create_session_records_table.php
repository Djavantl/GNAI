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
        Schema::create('session_records', function (Blueprint $table) {
            $table->id();

            // Relacionamentos
            $table->foreignId('attendance_sessions_id')
              ->constrained('attendance_sessions')
              ->cascadeOnDelete();

            // Controle temporal
            $table->date('record_date')->useCurrent();
            $table->string('duration');

            // Atividades e estratégias
            $table->longText('activities_performed');
            $table->longText('strategies_used')->nullable();
            $table->longText('resources_used')->nullable();
            $table->longText('adaptations_made')->nullable();

            // Comportamento e participação
            $table->string('student_participation');
            $table->string('engagement_level')->nullable(); 
            $table->longText('observed_behavior')->nullable();
            $table->longText('response_to_activities')->nullable();

            // Desenvolvimento / evolução
            $table->longText('development_evaluation');
            $table->longText('progress_indicators')->nullable();

            // Encaminhamentos
            $table->longText('recommendations')->nullable();
            $table->longText('next_session_adjustments')->nullable();
            $table->boolean('external_referral_needed')->default(false);
            $table->text('general_observations')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_records');
    }
};
