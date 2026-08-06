<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedagogical_record_guardians', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pedagogical_record_id')
                ->constrained('pedagogical_records')
                ->cascadeOnDelete();
            $table->foreignId('guardian_id')
                ->constrained('student_guardians')
                ->restrictOnDelete();
            $table->timestamps();
            $table->unique(
                ['pedagogical_record_id', 'guardian_id'],
                'ped_record_guardians_record_guardian_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedagogical_record_guardians');
    }
};
