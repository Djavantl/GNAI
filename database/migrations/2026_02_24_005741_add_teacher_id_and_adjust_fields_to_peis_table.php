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
        Schema::table('peis', function (Blueprint $table) {
            // 1. Adiciona teacher_id
            $table->foreignId('teacher_id')
                ->after('discipline_id')
                ->nullable()
                ->constrained('teachers')
                ->nullOnDelete();

            // 2. Torna teacher_name opcional
            $table->string('teacher_name')->nullable()->change();

            // 3. Remove a chave estrangeira antiga e a coluna professional_id
            $table->dropForeign(['professional_id']);
            $table->dropColumn('professional_id');

            // 4. CRIA a coluna creator_id (id do user) e define a chave estrangeira
            $table->foreignId('creator_id')
                ->after('student_id')
                ->constrained('users')
                ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peis', function (Blueprint $table) {
            // Remove o que foi criado
            $table->dropForeign(['teacher_id']);
            $table->dropColumn(['teacher_id']);
            
            $table->dropForeign(['creator_id']);
            $table->dropColumn('creator_id');

            $table->foreignId('professional_id')->constrained('professionals')->cascadeOnDelete();

            $table->string('teacher_name')->nullable(false)->change();
        });
    }
};