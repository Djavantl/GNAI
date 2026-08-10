<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->renameForeignKey(
            table: 'aee_records',
            column: 'attendance_session_id',
            legacyName: 'session_records_attendance_session_id_foreign',
            currentName: 'aee_records_attendance_session_id_foreign',
            referencedTable: 'attendance_sessions',
        );

        $this->renameForeignKey(
            table: 'aee_student_evaluations',
            column: 'student_id',
            legacyName: 'student_session_evaluations_student_id_foreign',
            currentName: 'aee_student_evaluations_student_id_foreign',
            referencedTable: 'students',
        );
    }

    public function down(): void
    {
        $this->renameForeignKey(
            table: 'aee_records',
            column: 'attendance_session_id',
            legacyName: 'aee_records_attendance_session_id_foreign',
            currentName: 'session_records_attendance_session_id_foreign',
            referencedTable: 'attendance_sessions',
        );

        $this->renameForeignKey(
            table: 'aee_student_evaluations',
            column: 'student_id',
            legacyName: 'aee_student_evaluations_student_id_foreign',
            currentName: 'student_session_evaluations_student_id_foreign',
            referencedTable: 'students',
        );
    }

    private function renameForeignKey(
        string $table,
        string $column,
        string $legacyName,
        string $currentName,
        string $referencedTable,
    ): void {
        if ($this->foreignKeyExists($table, $legacyName)) {
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$legacyName}`");
        }

        if (! $this->foreignKeyExists($table, $currentName)) {
            if ($this->indexExists($table, $legacyName)) {
                DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$legacyName}`");
            }

            DB::statement(
                "ALTER TABLE `{$table}` "
                ."ADD CONSTRAINT `{$currentName}` "
                ."FOREIGN KEY (`{$column}`) "
                ."REFERENCES `{$referencedTable}` (`id`) "
                .'ON DELETE CASCADE'
            );
        }
    }

    private function foreignKeyExists(string $table, string $constraint): bool
    {
        $result = DB::selectOne(
            <<<'SQL'
                SELECT COUNT(*) AS aggregate
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE CONSTRAINT_SCHEMA = DATABASE()
                  AND TABLE_NAME = ?
                  AND CONSTRAINT_NAME = ?
                  AND CONSTRAINT_TYPE = 'FOREIGN KEY'
            SQL,
            [$table, $constraint],
        );

        return (int) $result->aggregate > 0;
    }

    private function indexExists(string $table, string $index): bool
    {
        $result = DB::selectOne(
            <<<'SQL'
                SELECT COUNT(*) AS aggregate
                FROM information_schema.STATISTICS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = ?
                  AND INDEX_NAME = ?
            SQL,
            [$table, $index],
        );

        return (int) $result->aggregate > 0;
    }
};
