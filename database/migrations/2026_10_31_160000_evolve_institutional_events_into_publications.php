<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * A agenda institucional passa a ser a base das publicações do site:
 * eventos continuam existindo (has_schedule = true), mas notícias,
 * comunicados, artigos e registros podem ser publicados sem datas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('institutional_events', function (Blueprint $table): void {
            $table->date('start_date')->nullable()->change();

            $table->string('slug', 180)->nullable()->unique()->after('title');
            $table->string('summary', 500)->nullable()->after('slug');
            $table->string('type', 30)->default('event')->after('summary');
            $table->string('category', 100)->nullable()->after('type');
            $table->boolean('has_schedule')->default(true)->after('category');
            $table->foreignId('author_id')->nullable()->after('audience')
                ->constrained('users')->nullOnDelete();
            $table->string('link_url', 500)->nullable()->after('author_id');
            $table->string('link_label', 80)->nullable()->after('link_url');
            $table->string('cover_path')->nullable()->after('link_label');
            $table->string('cover_alt')->nullable()->after('cover_path');
            $table->timestamp('published_at')->nullable()->after('is_active');
            $table->boolean('is_featured')->default(false)->after('published_at');
            $table->string('seo_title')->nullable()->after('is_featured');
            $table->string('seo_description', 300)->nullable()->after('seo_title');
        });

        Schema::create('institutional_event_media', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('institutional_event_id')
                ->constrained('institutional_events')->cascadeOnDelete();
            $table->string('path');
            $table->string('alt_text');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('institutional_event_speakers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('institutional_event_id')
                ->constrained('institutional_events')->cascadeOnDelete();
            $table->foreignId('professional_id')
                ->constrained('professionals')->cascadeOnDelete();
            $table->unsignedInteger('position')->default(0);
            $table->unique(['institutional_event_id', 'professional_id'], 'event_speaker_unique');
        });

        $used = [];
        DB::table('institutional_events')
            ->select(['id', 'title', 'created_at'])
            ->orderBy('id')
            ->get()
            ->each(function (object $event) use (&$used): void {
                $base = Str::limit(Str::slug($event->title) ?: 'publicacao', 160, '');
                $slug = $base;
                $suffix = 2;

                while (isset($used[$slug])) {
                    $slug = "{$base}-{$suffix}";
                    $suffix++;
                }

                $used[$slug] = true;

                DB::table('institutional_events')->where('id', $event->id)->update([
                    'slug' => $slug,
                    'published_at' => $event->created_at,
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('institutional_event_speakers');
        Schema::dropIfExists('institutional_event_media');

        Schema::table('institutional_events', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('author_id');
            $table->dropUnique(['slug']);
            $table->dropColumn([
                'slug', 'summary', 'type', 'category', 'has_schedule',
                'link_url', 'link_label', 'cover_path', 'cover_alt',
                'published_at', 'is_featured', 'seo_title', 'seo_description',
            ]);
        });
    }
};
