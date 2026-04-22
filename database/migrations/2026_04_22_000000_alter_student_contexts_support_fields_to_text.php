<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_contexts', function (Blueprint $table) {
            $table->text('needs_mobility_support')->nullable()->change();
            $table->text('needs_communication_support')->nullable()->change();
            $table->text('needs_pedagogical_adaptation')->nullable()->change();
            $table->text('uses_assistive_technology')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('student_contexts', function (Blueprint $table) {
            $table->boolean('needs_mobility_support')->default(false)->change();
            $table->boolean('needs_communication_support')->default(false)->change();
            $table->boolean('needs_pedagogical_adaptation')->default(false)->change();
            $table->boolean('uses_assistive_technology')->default(false)->change();
        });
    }
};
