<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students_deficiencies', function (Blueprint $table) {
            $table->dropColumn('uses_support_resources');
        });
    }

    public function down(): void
    {
        Schema::table('students_deficiencies', function (Blueprint $table) {
            $table->boolean('uses_support_resources')->default(false)->after('severity');
        });
    }
};
