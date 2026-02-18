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

        
        $table->foreignId('attendance_session_id')
            ->constrained('attendance_sessions')
            ->cascadeOnDelete();

        $table->string('duration'); 
        $table->longText('activities_performed');
        $table->longText('strategies_used')->nullable();
        $table->longText('resources_used')->nullable();
        $table->text('general_observations')->nullable();

        $table->timestamps();
        $table->softDeletes();
        });

        Schema::create('student_session_evaluations', function (Blueprint $table) {
            $table->id();

            // Relacionamento com o registro geral da sessão
            $table->foreignId('session_record_id')
                ->constrained('session_records')
                ->cascadeOnDelete();

            // Relacionamento com o aluno específico
            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            // Regra de Presença
            $table->boolean('is_present')->default(true);
            $table->text('absence_reason')->nullable();

            // Comportamento e Adaptações (COMO o aluno reagiu)
            $table->longText('adaptations_made')->nullable();
            $table->longText('student_participation')->nullable(); 

            // Desenvolvimento e Evolução
            $table->longText('development_evaluation')->nullable();
            $table->longText('progress_indicators')->nullable();
            $table->longText('recommendations')->nullable();
            $table->longText('next_session_adjustments')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Garante que um aluno não tenha duas avaliações para o mesmo registro de sessão
            $table->unique(['session_record_id', 'student_id'], 'unique_student_evaluation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_records');
        Schema::dropIfExists('student_session_evaluations');
    }
};
