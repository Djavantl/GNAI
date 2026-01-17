<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('resource_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('for_assistive_technology')->default(false);
            $table->boolean('for_educational_material')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('type_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('label');
            $table->enum('field_type', ['string','integer','decimal','boolean','date','text']);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('type_attribute_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_id')
                ->constrained('resource_types')
                ->cascadeOnDelete();
            $table->foreignId('attribute_id')
                ->constrained('type_attributes')
                ->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['type_id','attribute_id'], 'type_attribute_unique');
        });

        Schema::create('resource_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id');
            $table->string('resource_type');

            $table->foreignId('attribute_id')
                ->constrained('type_attributes')
                ->cascadeOnDelete();
            $table->text('value')->nullable();
            $table->timestamps();

            $table->index(['resource_id','resource_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_attribute_values');
        Schema::dropIfExists('type_attribute_assignments');
        Schema::dropIfExists('type_attributes');
        Schema::dropIfExists('resource_types');
    }
};
