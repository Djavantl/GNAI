<?php

declare(strict_types=1);

namespace App\Domains\Audit\Domain\Contracts;

use App\Domains\Audit\Domain\ValueObjects\AuditedField;

/**
 * Model cujas criações, alterações e exclusões entram na auditoria do sistema.
 * Implementado junto com a trait `AuditsChanges`, que fornece os padrões dos métodos opcionais.
 */
interface Auditable
{
    /** Apelido estável gravado no histórico ('student'). Nunca mude: registros antigos dependem dele. */
    public static function auditType(): string;

    /** Nome do tipo para os filtros ('Aluno'). */
    public static function auditLabel(): string;

    /**
     * Lista branca: só estes campos são gravados, com rótulo e formato para a leitura em linguagem simples.
     *
     * @return array<string, AuditedField>
     */
    public static function auditedFields(): array;

    /**
     * Colunas deliberadamente fora da auditoria, além de id e timestamps.
     *
     * @return list<string>
     */
    public static function unauditedFields(): array;

    /** Como o registro aparece na frase, com artigo: "o aluno Maria Souza", "a sessão #12". */
    public function auditDescription(): string;

    /**
     * Valores fora das colunas (pivôs) que entram na foto de criação e exclusão.
     *
     * @return array<string, mixed>
     */
    public function auditExtraValues(): array;

    /** Guarda os valores adicionais antes da exclusão (fornecido pela trait `AuditsChanges`). */
    public function snapshotAuditExtraValues(): void;

    /**
     * Valores adicionais da foto de exclusão: os guardados antes do DELETE (fornecido pela trait).
     *
     * @return array<string, mixed>
     */
    public function auditExtraValuesForDeletion(): array;
}
