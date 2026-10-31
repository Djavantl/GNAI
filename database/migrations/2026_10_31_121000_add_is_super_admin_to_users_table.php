<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_super_admin')->default(false)->after('is_admin');
        });

        // Contas administrativas criadas pelo terminal (sem vínculo com a equipe)
        // passam a ser administradores superiores do Gnai.
        DB::table('users')
            ->where('is_admin', true)
            ->whereNull('professional_id')
            ->whereNull('teacher_id')
            ->update(['is_super_admin' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_super_admin');
        });
    }
};
