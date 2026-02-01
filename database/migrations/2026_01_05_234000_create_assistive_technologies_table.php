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
            $table->foreignId('type_id')
                ->nullable()
                ->constrained('resource_types')
                ->nullOnDelete();

            $table->string('asset_code', 50)->nullable()->unique();
            $table->integer('quantity')->nullable();
            $table->integer('quantity_available')->nullable();
            $table->boolean('requires_training')->default(false);
            $table->text('notes')->nullable();
            $table->string('conservation_state')->default('novo');
            $table->foreignId('status_id')
                ->nullable()
                ->constrained('resource_statuses')
                ->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
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
