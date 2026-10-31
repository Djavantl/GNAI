<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('site_name');
            $table->string('tagline')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('primary_color', 7)->default('#322b75');
            $table->string('contact_email')->nullable();
            $table->string('contact_phone', 40)->nullable();
            $table->string('address')->nullable();
            $table->json('social_links')->nullable();
            $table->string('footer_text', 500)->nullable();
            $table->json('sections')->nullable();
            $table->timestamps();
        });

        Schema::table('professionals', function (Blueprint $table): void {
            $table->boolean('show_on_website')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table): void {
            $table->dropColumn('show_on_website');
        });

        Schema::dropIfExists('website_settings');
    }
};
