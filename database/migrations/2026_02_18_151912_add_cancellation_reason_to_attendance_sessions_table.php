<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('attendance_sessions', function (Blueprint $table) {
            // Adiciona a coluna como text (para motivos longos) e nullable (para sessões não canceladas)
            $table->text('cancellation_reason')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->dropColumn('cancellation_reason');
        });
    }
};
