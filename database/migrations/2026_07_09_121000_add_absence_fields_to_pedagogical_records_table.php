<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedagogical_records', function (Blueprint $table) {
            $table->boolean('is_present')->default(true)->after('duration');
            $table->text('absence_reason')->nullable()->after('is_present');
            $table->longText('planned_performed_activities')->nullable()->change();
            $table->longText('pedagogical_record')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pedagogical_records', function (Blueprint $table) {
            $table->dropColumn(['is_present', 'absence_reason']);
            $table->longText('planned_performed_activities')->nullable(false)->change();
            $table->longText('pedagogical_record')->nullable(false)->change();
        });
    }
};
