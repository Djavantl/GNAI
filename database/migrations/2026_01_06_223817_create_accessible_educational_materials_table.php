<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {

        Schema::create('accessibility_features', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('accessible_educational_materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_digital')->default(false);
            $table->text('notes')->nullable();
            $table->string('asset_code', 50)->nullable()->unique();
            $table->integer('quantity')->nullable();
            $table->integer('quantity_available')->nullable();
            $table->string('conservation_state')->default('novo');
            $table->foreignId('status_id')->nullable()->constrained('resource_statuses')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('accessible_educational_material_training', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accessible_educational_material_id')->constrained('accessible_educational_materials')->cascadeOnDelete()->name('aem_training_material_fk');
            $table->foreignId('training_id')->constrained('trainings')->cascadeOnDelete()->name('aem_training_training_fk');
            $table->unique(['accessible_educational_material_id', 'training_id'], 'aem_training_unique');
            $table->timestamps();
        });

        Schema::create('accessible_educational_material_accessibility', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accessible_educational_material_id')->constrained('accessible_educational_materials')->cascadeOnDelete()->name('aem_access_material_fk');
            $table->foreignId('accessibility_feature_id')->constrained('accessibility_features')->cascadeOnDelete()->name('aem_access_feature_fk');
            $table->unique(['accessible_educational_material_id', 'accessibility_feature_id'], 'aem_accessibility_unique');
        });

        Schema::create('accessible_educational_material_deficiency', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accessible_educational_material_id')->constrained('accessible_educational_materials')->cascadeOnDelete()->name('aem_def_material_fk');
            $table->foreignId('deficiency_id')->constrained('deficiencies')->cascadeOnDelete()->name('aem_def_deficiency_fk');
            $table->timestamps();
            $table->unique(['accessible_educational_material_id', 'deficiency_id'], 'aem_deficiency_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accessible_educational_material_deficiency');
        Schema::dropIfExists('accessible_educational_material_accessibility');
        Schema::dropIfExists('accessible_educational_material_training');
        Schema::dropIfExists('accessible_educational_materials');
        Schema::dropIfExists('accessibility_features');
    }
};
