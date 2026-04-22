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
        Schema::table('pei_disciplines', function (Blueprint $table) {
            // Torna o campo opcional no banco de dados
            $table->text('opinion')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pei_disciplines', function (Blueprint $table) {
            // Reverte para obrigatório caso precise dar rollback
            $table->text('opinion')->nullable(false)->change();
        });
    }
};
