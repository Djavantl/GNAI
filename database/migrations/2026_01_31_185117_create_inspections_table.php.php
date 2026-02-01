<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->morphs('inspectable');
            $table->string('state')->nullable();
            $table->string('type');

            $table->date('inspection_date');
            $table->text('description')->nullable();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });

        Schema::create('inspection_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')
                ->constrained('inspections')
                ->cascadeOnDelete();

            $table->string('path');
            $table->string('original_name')->nullable();
            $table->string('mime_type', 50)->nullable();
            $table->integer('size')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_images');
        Schema::dropIfExists('inspections');
    }
};
