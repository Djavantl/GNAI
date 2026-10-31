<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Registra como cada participante atua na publicação (palestrante, mediador...).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publication_speakers', function (Blueprint $table): void {
            $table->string('participation', 100)->nullable()->after('position');
        });
    }

    public function down(): void
    {
        Schema::table('publication_speakers', function (Blueprint $table): void {
            $table->dropColumn('participation');
        });
    }
};
