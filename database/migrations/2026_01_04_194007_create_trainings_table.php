<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabela principal de treinamentos
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('url')->nullable();

            $table->morphs('trainable');

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabela de arquivos do treinamento
        Schema::create('training_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')
                ->constrained('trainings')
                ->cascadeOnDelete();

            $table->string('path');
            $table->string('original_name')->nullable();
            $table->string('mime_type', 50)->nullable();
            $table->integer('size')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_files');
        Schema::dropIfExists('trainings');
    }
};
