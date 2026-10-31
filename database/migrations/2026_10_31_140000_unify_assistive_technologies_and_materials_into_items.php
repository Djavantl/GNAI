<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Unifica tecnologias assistivas e materiais pedagógicos acessíveis na tabela `items`.
 *
 * As tecnologias assistivas mantêm o mesmo ID; os materiais recebem o ID antigo somado
 * ao maior ID de tecnologia existente (incluindo excluídas). Essa regra determinística
 * remapeia empréstimos, fila de espera, inspeções, auditoria e notificações.
 */
return new class extends Migration
{
    private const ASSISTIVE_TECHNOLOGY = 'assistive_technology';

    private const MATERIAL = 'accessible_educational_material';

    private const ITEM = 'item';

    /**
     * @var array<string, string>
     */
    private const PERMISSION_ACTIONS = [
        'index' => 'Visualizar Itens',
        'create' => 'Criar Itens',
        'store' => 'Salvar Itens',
        'show' => 'Visualizar Itens',
        'edit' => 'Editar Itens',
        'update' => 'Editar Itens',
        'destroy' => 'Excluir Itens',
        'pdf' => 'Gerar PDF Itens',
        'inspection.show' => 'Visualizar vistoria de Itens',
    ];

    public function up(): void
    {
        $this->ensureNoAssetCodeConflicts();

        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('item_type')->index();
            $table->string('name');
            $table->boolean('is_digital')->default(false);
            $table->text('notes')->nullable();
            $table->string('asset_code', 50)->nullable()->unique();
            $table->integer('quantity')->nullable();
            $table->integer('quantity_available')->nullable();
            $table->string('conservation_state')->default('novo');
            $table->string('status')->default('available');
            $table->boolean('is_loanable')->default(true);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('item_deficiency', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('deficiency_id')->constrained('deficiencies')->cascadeOnDelete();
            $table->unique(['item_id', 'deficiency_id']);
            $table->timestamps();
        });

        Schema::create('item_accessibility_feature', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('accessibility_feature_id')->constrained('accessibility_features')->cascadeOnDelete();
            $table->unique(['item_id', 'accessibility_feature_id'], 'item_feature_unique');
            $table->timestamps();
        });

        DB::transaction(function (): void {
            $offset = (int) (DB::table('assistive_technologies')->max('id') ?? 0);

            $this->copyItems('assistive_technologies', self::ASSISTIVE_TECHNOLOGY, 0);
            $this->copyItems('accessible_educational_materials', self::MATERIAL, $offset);

            $this->copyPivot('assistive_technology_deficiency', 'assistive_technology_id', 'item_deficiency', 'deficiency_id', 0);
            $this->copyPivot('accessible_educational_material_deficiency', 'accessible_educational_material_id', 'item_deficiency', 'deficiency_id', $offset);
            $this->copyPivot('accessible_educational_material_accessibility', 'accessible_educational_material_id', 'item_accessibility_feature', 'accessibility_feature_id', $offset);

            foreach ([
                'loans' => 'loanable',
                'waitlists' => 'waitlistable',
                'inspections' => 'inspectable',
            ] as $table => $morph) {
                $this->remapMorph($table, $morph, $offset);
            }

            $this->remapAuditRecords($offset);
            $this->remapLoanNotificationLinks($offset);
            $this->migratePermissions();
        });

        Schema::dropIfExists('assistive_technology_deficiency');
        Schema::dropIfExists('accessible_educational_material_deficiency');
        Schema::dropIfExists('accessible_educational_material_accessibility');
        Schema::dropIfExists('assistive_technologies');
        Schema::dropIfExists('accessible_educational_materials');

        // Atualiza o manifest de permissões usado pelo Gate e invalida o cache.
        if (! app()->runningUnitTests()) {
            Artisan::call('permissions:sync');
        }
    }

    public function down(): void
    {
        throw new RuntimeException(
            'A unificação de itens não pode ser revertida automaticamente. Restaure o backup anterior à migração.'
        );
    }

    private function ensureNoAssetCodeConflicts(): void
    {
        $conflicts = DB::table('assistive_technologies')
            ->join(
                'accessible_educational_materials',
                'assistive_technologies.asset_code',
                '=',
                'accessible_educational_materials.asset_code',
            )
            ->whereNotNull('assistive_technologies.asset_code')
            ->pluck('assistive_technologies.asset_code')
            ->unique()
            ->values()
            ->all();

        if ($conflicts !== []) {
            throw new RuntimeException(
                'Há códigos de patrimônio repetidos entre tecnologias assistivas e materiais pedagógicos: '
                .implode(', ', $conflicts)
                .'. Corrija-os antes de unificar os itens.'
            );
        }
    }

    private function copyItems(string $sourceTable, string $type, int $offset): void
    {
        DB::table($sourceTable)
            ->orderBy('id')
            ->chunkById(500, function ($rows) use ($type, $offset): void {
                DB::table('items')->insert(
                    $rows->map(static fn (object $row): array => [
                        'id' => $row->id + $offset,
                        'item_type' => $type,
                        'name' => $row->name,
                        'is_digital' => $row->is_digital,
                        'notes' => $row->notes,
                        'asset_code' => $row->asset_code,
                        'quantity' => $row->quantity,
                        'quantity_available' => $row->quantity_available,
                        'conservation_state' => $row->conservation_state,
                        'status' => $row->status,
                        'is_loanable' => $row->is_loanable,
                        'is_active' => $row->is_active,
                        'deleted_at' => $row->deleted_at,
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ])->all(),
                );
            });
    }

    private function copyPivot(string $sourceTable, string $sourceKey, string $targetTable, string $relatedKey, int $offset): void
    {
        $hasTimestamps = Schema::hasColumn($sourceTable, 'created_at');

        DB::table($sourceTable)
            ->orderBy('id')
            ->chunkById(500, function ($rows) use ($sourceKey, $targetTable, $relatedKey, $offset, $hasTimestamps): void {
                DB::table($targetTable)->insertOrIgnore(
                    $rows->map(static fn (object $row): array => [
                        'item_id' => $row->{$sourceKey} + $offset,
                        $relatedKey => $row->{$relatedKey},
                        'created_at' => $hasTimestamps ? $row->created_at : now(),
                        'updated_at' => $hasTimestamps ? $row->updated_at : now(),
                    ])->all(),
                );
            });
    }

    private function remapMorph(string $table, string $morph, int $offset): void
    {
        DB::table($table)
            ->where("{$morph}_type", self::MATERIAL)
            ->update([
                "{$morph}_type" => self::ITEM,
                "{$morph}_id" => DB::raw("{$morph}_id + {$offset}"),
            ]);

        DB::table($table)
            ->where("{$morph}_type", self::ASSISTIVE_TECHNOLOGY)
            ->update(["{$morph}_type" => self::ITEM]);
    }

    private function remapAuditRecords(int $offset): void
    {
        DB::table('audit_records')
            ->where('entity_type', self::MATERIAL)
            ->update([
                'entity_type' => self::ITEM,
                'entity_id' => DB::raw("CAST(entity_id AS UNSIGNED) + {$offset}"),
            ]);

        DB::table('audit_records')
            ->where('entity_type', self::ASSISTIVE_TECHNOLOGY)
            ->update(['entity_type' => self::ITEM]);
    }

    /**
     * Notificações da fila de espera guardam o link para registrar o empréstimo com o ID do item.
     */
    private function remapLoanNotificationLinks(int $offset): void
    {
        if ($offset === 0) {
            return;
        }

        DB::table('notifications')
            ->where('data', 'like', '%item_type='.self::MATERIAL.'%')
            ->orderBy('id')
            ->each(function (object $notification) use ($offset): void {
                $data = json_decode((string) $notification->data, true);

                if (! is_array($data) || ! is_string($data['url'] ?? null)) {
                    return;
                }

                $data['url'] = preg_replace_callback(
                    '/([?&]item_id=)(\d+)/',
                    static fn (array $match): string => $match[1].((int) $match[2] + $offset),
                    $data['url'],
                );

                DB::table('notifications')
                    ->where('id', $notification->id)
                    ->update(['data' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]);
            });
    }

    private function migratePermissions(): void
    {
        foreach (self::PERMISSION_ACTIONS as $action => $name) {
            $legacyIds = DB::table('permissions')
                ->whereIn('slug', ["assistive-technology.{$action}", "material.{$action}"])
                ->pluck('id');

            $permissionId = DB::table('permissions')->where('slug', "item.{$action}")->value('id')
                ?? DB::table('permissions')->insertGetId([
                    'name' => $name,
                    'slug' => "item.{$action}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            if ($legacyIds->isEmpty()) {
                continue;
            }

            $positionIds = DB::table('permission_position')
                ->whereIn('permission_id', $legacyIds)
                ->pluck('position_id')
                ->merge(
                    DB::table('permission_position')->where('permission_id', $permissionId)->pluck('position_id')
                )
                ->unique();

            DB::table('permission_position')->where('permission_id', $permissionId)->delete();
            DB::table('permission_position')->insert(
                $positionIds->map(static fn (int|string $positionId): array => [
                    'position_id' => (int) $positionId,
                    'permission_id' => $permissionId,
                ])->values()->all(),
            );

            $grantedToTeachers = DB::table('teacher_global_permissions')
                ->whereIn('permission_id', $legacyIds)
                ->exists();

            if (
                $grantedToTeachers
                && ! DB::table('teacher_global_permissions')->where('permission_id', $permissionId)->exists()
            ) {
                DB::table('teacher_global_permissions')->insert([
                    'permission_id' => $permissionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('permission_position')->whereIn('permission_id', $legacyIds)->delete();
            DB::table('teacher_global_permissions')->whereIn('permission_id', $legacyIds)->delete();
            DB::table('permissions')->whereIn('id', $legacyIds)->delete();
        }
    }
};
