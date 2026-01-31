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
        Schema::create('students_deficiencies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('deficiency_id')
                ->constrained('deficiencies')
                ->restrictOnDelete();

            $table->enum('severity', [
                'mild',
                'moderate',
                'severe'
            ])->nullable();

            $table->boolean('uses_support_resources')->default(false);

            $table->text('notes')->nullable();

            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students_deficiencies');
    }
};
