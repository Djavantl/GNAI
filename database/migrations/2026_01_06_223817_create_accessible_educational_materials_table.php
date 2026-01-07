<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('accessible_educational_material_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('accessibility_features', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });


        Schema::create('accessible_educational_materials', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type')->nullable();
            $table->string('format')->nullable();
            $table->string('language', 10)->nullable();
            $table->string('isbn')->nullable()->unique();
            $table->string('publisher')->nullable();
            $table->string('edition')->nullable();
            $table->date('publication_date')->nullable();
            $table->integer('pages')->nullable();
            $table->string('asset_code', 50)->nullable()->unique();
            $table->string('location')->nullable();
            $table->string('conservation_state', 50)->nullable();
            $table->boolean('requires_training')->default(false);
            $table->decimal('cost', 10, 2)->nullable();

            $table->foreignId('accessible_educational_material_status_id')
                ->nullable()
                ->constrained('accessible_educational_material_statuses')
                ->nullOnDelete()
                ->name('aem_status_id_fk');

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'is_active']);
        });

        Schema::create('accessible_educational_material_accessibility', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accessible_educational_material_id')
                ->constrained('accessible_educational_materials')
                ->cascadeOnDelete()
                ->name('aem_accessibility_material_fk');

            $table->foreignId('accessibility_feature_id')
                ->constrained('accessibility_features')
                ->cascadeOnDelete()
                ->name('aem_accessibility_feature_fk');

            $table->unique(
                ['accessible_educational_material_id', 'accessibility_feature_id'],
                'aem_accessibility_unique'
            );
        });


        Schema::create('accessible_educational_material_deficiency', function (Blueprint $table) {
            $table->id();

            $table->foreignId('accessible_educational_material_id')
                ->constrained('accessible_educational_materials')
                ->cascadeOnDelete()
                ->name('aem_def_material_fk');

            $table->foreignId('deficiency_id')
                ->constrained('deficiencies')
                ->cascadeOnDelete()
                ->name('aem_def_deficiency_fk');

            $table->timestamps();

            $table->unique(
                ['accessible_educational_material_id', 'deficiency_id'],
                'aem_deficiency_unique'
            );
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('accessible_educational_material_deficiency');
        Schema::dropIfExists('accessible_educational_material_accessibility');
        Schema::dropIfExists('accessible_educational_materials');
        Schema::dropIfExists('accessibility_features');
        Schema::dropIfExists('accessible_educational_material_statuses');
    }
};
