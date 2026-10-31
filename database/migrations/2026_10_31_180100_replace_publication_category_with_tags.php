<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Substitui a categoria em texto livre das publicações pelo cadastro de
 * etiquetas. Cada categoria distinta já usada vira uma etiqueta ativa e é
 * vinculada às publicações que a usavam, antes da remoção da coluna.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publication_tag', function (Blueprint $table): void {
            $table->foreignId('publication_id')->constrained('publications')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete();
            $table->primary(['publication_id', 'tag_id']);
        });

        $now = now();

        DB::table('publications')
            ->whereNotNull('category')
            ->where('category', '<>', '')
            ->orderBy('id')
            ->get(['id', 'category'])
            ->each(function (object $publication) use ($now): void {
                $name = mb_substr(trim((string) $publication->category), 0, 100);

                if ($name === '') {
                    return;
                }

                $tagId = DB::table('tags')->where('name', $name)->value('id')
                    ?? DB::table('tags')->insertGetId([
                        'name' => $name,
                        'is_active' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                DB::table('publication_tag')->insertOrIgnore([
                    'publication_id' => $publication->id,
                    'tag_id' => $tagId,
                ]);
            });

        Schema::table('publications', function (Blueprint $table): void {
            $table->dropColumn('category');
        });
    }

    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table): void {
            $table->string('category', 100)->nullable()->after('type');
        });

        DB::table('publication_tag')
            ->join('tags', 'tags.id', '=', 'publication_tag.tag_id')
            ->orderBy('tags.name')
            ->get(['publication_tag.publication_id', 'tags.name'])
            ->groupBy('publication_id')
            ->each(function ($rows, $publicationId): void {
                DB::table('publications')
                    ->where('id', $publicationId)
                    ->update(['category' => $rows->first()->name]);
            });

        Schema::dropIfExists('publication_tag');
    }
};
