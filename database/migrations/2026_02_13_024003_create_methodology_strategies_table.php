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
        Schema::create('methodologies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pei_id')->constrained('peis')->onDelete('cascade');
            $table->string('title'); 
            $table->text('description'); 
            $table->text('resources_used')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('methodologies');
    }
};
