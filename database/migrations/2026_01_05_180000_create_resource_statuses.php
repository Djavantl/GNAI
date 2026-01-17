<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('resource_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('blocks_loan')->default(false);
            $table->boolean('blocks_access')->default(false);
            $table->boolean('for_assistive_technology')->default(false);
            $table->boolean('for_educational_material')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('resource_statuses');
    }
};
