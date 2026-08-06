<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedagogical_record_disciplines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pedagogical_record_id')
                ->constrained('pedagogical_records')
                ->cascadeOnDelete();
            $table->foreignId('discipline_id')
                ->constrained('disciplines')
                ->restrictOnDelete();
            $table->string('category');
            $table->timestamps();

            $table->unique(
                ['pedagogical_record_id', 'discipline_id', 'category'],
                'ped_record_discipline_category_unique',
            );
        });

        Schema::table('pedagogical_records', function (Blueprint $table): void {
            $table->dropColumn('disciplines_with_failure_or_academic_risk');
        });
    }

    public function down(): void
    {
        Schema::table('pedagogical_records', function (Blueprint $table): void {
            $table->longText('disciplines_with_failure_or_academic_risk')->nullable()
                ->after('strategies_and_resources_adopted');
        });

        Schema::dropIfExists('pedagogical_record_disciplines');
    }
};
