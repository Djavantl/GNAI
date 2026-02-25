<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('barrier_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('district')->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->integer('default_zoom')->default(16);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

        });

        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')
                ->constrained('institutions')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('type')->nullable();
            $table->text('description')->nullable();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('google_place_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('barriers', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->text('description')->nullable();

            $table->foreignId('registered_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('institution_id')
                ->constrained('institutions')
                ->cascadeOnDelete();

            $table->foreignId('barrier_category_id')
                ->constrained('barrier_categories')
                ->restrictOnDelete();

            $table->foreignId('location_id')
                ->nullable()
                ->constrained('locations')
                ->cascadeOnDelete();

            $table->foreignId('affected_student_id')
                ->nullable()
                ->constrained('students')
                ->nullOnDelete();

            $table->foreignId('affected_professional_id')
                ->nullable()
                ->constrained('professionals')
                ->nullOnDelete();

            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('location_specific_details')->nullable();
            $table->string('affected_person_name')->nullable();
            $table->string('affected_person_role')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->string('priority')->default('medium');
            $table->date('identified_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('barrier_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barrier_id')->constrained('barriers')->cascadeOnDelete();
            $table->unsignedTinyInteger('step_number');
            $table->string('status')->default('identified');

            // Usuários
            $table->foreignId('started_by_user_id')->nullable()->constrained('users')->nullOnDelete(); // Etapa 1
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Etapas 2-4
            $table->foreignId('validator_id')->nullable()->constrained('users')->nullOnDelete(); // Etapa 4

            // Campos comuns
            $table->text('observation')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Etapa 2
            $table->text('analyst_notes')->nullable();
            $table->text('justificativa_encerramento')->nullable();

            // Etapa 3
            $table->text('action_plan_description')->nullable();
            $table->date('intervention_start_date')->nullable();
            $table->date('estimated_completion_date')->nullable();
            $table->decimal('estimated_cost', 12, 2)->nullable();

            // Etapa 4
            $table->decimal('actual_cost', 12, 2)->nullable();
            $table->dateTime('resolution_date')->nullable();
            $table->text('delay_justification')->nullable();
            $table->text('resolution_summary')->nullable();
            $table->string('effectiveness_level')->nullable();
            $table->text('maintenance_instructions')->nullable();

            $table->timestamps();
            $table->unique(['barrier_id', 'step_number']);
        });

        Schema::create('barrier_deficiency', function (Blueprint $table) {
            $table->id();

            $table->foreignId('barrier_id')
                ->constrained('barriers')
                ->cascadeOnDelete();

            $table->foreignId('deficiency_id')
                ->constrained('deficiencies')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['barrier_id', 'deficiency_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barrier_deficiency');
        Schema::dropIfExists('barrier_stages');
        Schema::dropIfExists('barriers');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('institutions');
        Schema::dropIfExists('barrier_categories');
    }
};
