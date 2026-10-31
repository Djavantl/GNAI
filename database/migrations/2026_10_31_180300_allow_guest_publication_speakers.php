<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Permite participantes avulsos: cada linha aponta para um profissional
 * cadastrado ou guarda apenas o nome digitado.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publication_speakers', function (Blueprint $table): void {
            $table->unsignedBigInteger('professional_id')->nullable()->change();
            $table->string('name', 150)->nullable()->after('professional_id');
        });
    }

    public function down(): void
    {
        DB::table('publication_speakers')->whereNull('professional_id')->delete();

        Schema::table('publication_speakers', function (Blueprint $table): void {
            $table->dropColumn('name');
            $table->unsignedBigInteger('professional_id')->nullable(false)->change();
        });
    }
};
