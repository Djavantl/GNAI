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
            $table->enum('type', [
                'software',
                'hardware',
                'service',
                'methodology',
                'material'
            ]);
            $table->integer('quantity')->nullable();
            $table->string('asset_code')->nullable();
            $table->enum('conservation_state', [
                'new',
                'good',
                'regular',
                'poor',
                'unusable'
            ])->nullable();
            $table->string('target_audience')->nullable();
            $table->boolean('requires_training')->default(false);
            $table->text('notes')->nullable();
            $table->foreignId('assistive_technology_status_id')
                ->constrained('assistive_technology_statuses');

            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('assistive_technologies');
    }
};
