<?php

use App\Enums\InclusiveRadar\MaintenanceStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabela principal de manutenção
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();

            // Relacionamento polimórfico com o recurso (TA, MPA, etc)
            $table->morphs('maintainable');

            // Status geral da manutenção (pendente ou concluída)
            $table->string('status')->default(MaintenanceStatus::PENDING->value);

            $table->timestamps();

            $table->index(['maintainable_id', 'maintainable_type']);
        });

        // Tabela de etapas da manutenção
        Schema::create('maintenance_stages', function (Blueprint $table) {
            $table->id();

            // Referência para a manutenção
            $table->foreignId('maintenance_id')
                ->constrained('maintenances')
                ->cascadeOnDelete();

            // Número da etapa (1 = inicial, 2 = final)
            $table->unsignedTinyInteger('step_number');

            // Usuário que iniciou a etapa (apenas para visualização)
            $table->foreignId('started_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Usuário que concluiu a etapa (só é preenchido ao finalizar)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Campos específicos por etapa
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('real_cost', 10, 2)->nullable();
            $table->text('observation')->nullable();
            $table->text('damage_description')->nullable();

            // Timestamp de conclusão da etapa
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            // Garante que cada etapa exista apenas uma vez por manutenção
            $table->unique(['maintenance_id', 'step_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_stages');
        Schema::dropIfExists('maintenances');
    }
};
