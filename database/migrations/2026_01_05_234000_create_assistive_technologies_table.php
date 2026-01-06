<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assistive_technologies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type', 100)->nullable();
            $table->integer('quantity')->default(0);
            $table->string('asset_code', 50)->nullable()->unique();
            $table->string('conservation_state', 50)->nullable();
            $table->boolean('requires_training')->default(false);
            $table->text('notes')->nullable();
            $table->foreignId('assistive_technology_status_id')
                ->nullable()
                ->constrained('assistive_technology_statuses')
                ->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('assistive_technology_deficiency', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assistive_technology_id')
                ->constrained('assistive_technologies')
                ->onDelete('cascade');
            $table->foreignId('deficiency_id')
                ->constrained('deficiencies')
                ->onDelete('cascade');
            $table->timestamps();
            $table->unique(['assistive_technology_id', 'deficiency_id'], 'tech_def_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assistive_technology_deficiency');
        Schema::dropIfExists('assistive_technologies');
    }
};
