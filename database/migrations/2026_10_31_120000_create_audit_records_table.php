<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Histórico de alterações do sistema inteiro: uma linha por registro criado, alterado, excluído ou restaurado
 * numa operação. Substitui a antiga `audit_logs`, removida em 2026_08_03.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_records', function (Blueprint $table) {
            $table->id();
            $table->uuid('operation');
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_name')->nullable();
            $table->foreignId('impersonator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('impersonator_name')->nullable();
            $table->string('event', 20);
            $table->string('entity_type', 60);
            $table->string('entity_id', 64);
            $table->string('entity_description');
            $table->json('changes');
            $table->json('referenced_names')->nullable();
            $table->json('context')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('created_at');
            $table->index(['entity_type', 'entity_id']);
            $table->index('actor_id');
            $table->index('operation');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_records');
    }
};
