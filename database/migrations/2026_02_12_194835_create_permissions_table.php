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
        // Tabela de permissões (ex: 'aluno.create', 'aluno.view')
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome amigável: "Cadastrar Aluno"
            $table->string('slug')->unique(); // Identificador: "aluno.create"
            $table->timestamps();
        });

        // Tabela pivô: liga Cargo (Position) com Permissão
        Schema::create('permission_position', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions', 'permission_position');
    }
};
