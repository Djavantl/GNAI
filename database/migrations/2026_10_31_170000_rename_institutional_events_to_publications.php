<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Padroniza a antiga agenda institucional como "publicações" no banco:
 * tabelas, chave estrangeira, slugs de permissão (preservando quem já tinha
 * acesso) e o tipo gravado no histórico de auditoria.
 */
return new class extends Migration
{
    /** @var array<string, string> ação => rótulo, como em PermissionRegistry::humanize() */
    private const ACTIONS = [
        'index' => 'Visualizar',
        'show' => 'Visualizar',
        'create' => 'Criar',
        'store' => 'Salvar',
        'edit' => 'Editar',
        'update' => 'Editar',
        'destroy' => 'Excluir',
        'pdf' => 'Gerar PDF',
    ];

    public function up(): void
    {
        Schema::rename('institutional_events', 'publications');
        Schema::rename('institutional_event_media', 'publication_media');
        Schema::rename('institutional_event_speakers', 'publication_speakers');

        foreach (['publication_media', 'publication_speakers'] as $table) {
            Schema::table($table, function (Blueprint $table): void {
                $table->renameColumn('institutional_event_id', 'publication_id');
            });
        }

        foreach (self::ACTIONS as $action => $label) {
            $this->moveSlug("institutional-event.{$action}", "publication.{$action}", "{$label} Publicações");
        }

        $this->renameAuditType('institutional_event', 'publication');
    }

    public function down(): void
    {
        $this->renameAuditType('publication', 'institutional_event');

        foreach (self::ACTIONS as $action => $label) {
            $this->moveSlug("publication.{$action}", "institutional-event.{$action}", "{$label} Institutional Event");
        }

        foreach (['publication_media', 'publication_speakers'] as $table) {
            Schema::table($table, function (Blueprint $table): void {
                $table->renameColumn('publication_id', 'institutional_event_id');
            });
        }

        Schema::rename('publication_speakers', 'institutional_event_speakers');
        Schema::rename('publication_media', 'institutional_event_media');
        Schema::rename('publications', 'institutional_events');
    }

    /**
     * Renomeia a permissão mantendo o id (e, com ele, os vínculos com cargos e
     * professores). Se o slug novo já existir, transfere os vínculos para ele.
     */
    private function moveSlug(string $from, string $to, string $name): void
    {
        $legacyId = DB::table('permissions')->where('slug', $from)->value('id');

        if ($legacyId === null) {
            return;
        }

        $targetId = DB::table('permissions')->where('slug', $to)->value('id');

        if ($targetId === null) {
            DB::table('permissions')->where('id', $legacyId)->update([
                'slug' => $to,
                'name' => $name,
                'updated_at' => now(),
            ]);

            return;
        }

        $positionIds = DB::table('permission_position')
            ->where('permission_id', $legacyId)
            ->pluck('position_id')
            ->diff(DB::table('permission_position')->where('permission_id', $targetId)->pluck('position_id'));

        DB::table('permission_position')->insert(
            $positionIds->map(static fn (int|string $positionId): array => [
                'position_id' => (int) $positionId,
                'permission_id' => $targetId,
            ])->values()->all(),
        );

        if (
            DB::table('teacher_global_permissions')->where('permission_id', $legacyId)->exists()
            && ! DB::table('teacher_global_permissions')->where('permission_id', $targetId)->exists()
        ) {
            DB::table('teacher_global_permissions')->insert([
                'permission_id' => $targetId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('permission_position')->where('permission_id', $legacyId)->delete();
        DB::table('teacher_global_permissions')->where('permission_id', $legacyId)->delete();
        DB::table('permissions')->where('id', $legacyId)->delete();
    }

    private function renameAuditType(string $from, string $to): void
    {
        if (Schema::hasTable('audit_records')) {
            DB::table('audit_records')->where('entity_type', $from)->update(['entity_type' => $to]);
        }
    }
};
