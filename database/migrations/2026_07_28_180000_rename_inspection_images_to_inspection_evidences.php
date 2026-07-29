<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('inspection_images') && ! Schema::hasTable('inspection_evidences')) {
            Schema::rename('inspection_images', 'inspection_evidences');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('inspection_evidences') && ! Schema::hasTable('inspection_images')) {
            Schema::rename('inspection_evidences', 'inspection_images');
        }
    }
};
