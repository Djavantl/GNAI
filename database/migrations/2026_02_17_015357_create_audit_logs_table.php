<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {

            $table->id();

            // Quem fez a ação
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Tipo de ação
            $table->string('action');

            // Model auditado (polimórfico)
            $table->morphs('auditable');

            // Dados antes e depois
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            // Contexto extra
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
