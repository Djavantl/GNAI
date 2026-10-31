<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table): void {
            $table->longText('notes')->nullable()->change();
        });

        Schema::table('inspections', function (Blueprint $table): void {
            $table->longText('description')->nullable()->change();
        });
    }

    public function down(): void
    {
        // TEXT comporta até 65.535 bytes. Não permita truncar imagens/HTML já salvos.
        foreach (['items' => 'notes', 'inspections' => 'description'] as $table => $column) {
            if (DB::table($table)->whereRaw("OCTET_LENGTH({$column}) > 65535")->exists()) {
                throw new RuntimeException('Há conteúdo que excede o limite de TEXT. A reversão foi interrompida para preservar os dados.');
            }
        }

        Schema::table('items', function (Blueprint $table): void {
            $table->text('notes')->nullable()->change();
        });

        Schema::table('inspections', function (Blueprint $table): void {
            $table->text('description')->nullable()->change();
        });
    }
};
