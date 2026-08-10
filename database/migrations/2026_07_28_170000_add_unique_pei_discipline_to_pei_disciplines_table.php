<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pei_disciplines', function (Blueprint $table): void {
            $table->unique(['pei_id', 'discipline_id'], 'pei_disciplines_pei_id_discipline_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('pei_disciplines', function (Blueprint $table): void {
            $table->dropUnique('pei_disciplines_pei_id_discipline_id_unique');
        });
    }
};
