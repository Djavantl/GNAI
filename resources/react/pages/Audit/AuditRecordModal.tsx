import { useEffect, useState } from "react";
import Button from "@/components/UI/Button/Button";
import Modal from "@/components/UI/Modal/Modal";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import type { AuditField, AuditRecord, AuditView } from "./Audit.types";
import { auditDateTime, auditEventTone } from "./Audit.utils";

function FieldValue({ value }: { value: string | null }): React.JSX.Element {
    return value === null || value === ""
        ? <span className="auditRecord__empty">vazio</span>
        : <>{value}</>;
}

function ListChanges({ field }: { field: AuditField }): React.JSX.Element {
    const added = field.added ?? [];
    const removed = field.removed ?? [];

    if (!added.length && !removed.length) {
        return <span className="auditRecord__empty">sem itens</span>;
    }

    return (
        <ul className="auditRecord__list">
            {added.map((item) => (
                <li key={`added-${item}`} className="auditRecord__listItem auditRecord__listItem--added">
                    <i className="fa fa-plus" aria-hidden="true" />
                    <span className="auditRecord__srOnly">Adicionado: </span>
                    {item}
                </li>
            ))}
            {removed.map((item) => (
                <li key={`removed-${item}`} className="auditRecord__listItem auditRecord__listItem--removed">
                    <i className="fa fa-minus" aria-hidden="true" />
                    <span className="auditRecord__srOnly">Removido: </span>
                    {item}
                </li>
            ))}
        </ul>
    );
}

function PlainFields({ record }: { record: AuditRecord }): React.JSX.Element {
    const fields = record.plain.fields;
    const comparing = record.event === "updated";

    if (!fields.length) {
        return <p className="auditRecord__noFields">Nenhum campo registrado.</p>;
    }

    return (
        <div className="auditRecord__tableWrap">
            <table className="auditRecord__table">
                <thead>
                    <tr>
                        <th scope="col">Campo</th>
                        {comparing ? (
                            <>
                                <th scope="col">Antes</th>
                                <th scope="col">Depois</th>
                            </>
                        ) : (
                            <th scope="col">
                                {record.event === "deleted" ? "Valor ao excluir" : "Valor registrado"}
                            </th>
                        )}
                    </tr>
                </thead>
                <tbody>
                    {fields.map((field) => (
                        <tr key={field.field}>
                            <th scope="row">{field.label}</th>
                            {field.format === "list" ? (
                                <td colSpan={comparing ? 2 : 1}>
                                    <ListChanges field={field} />
                                </td>
                            ) : comparing ? (
                                <>
                                    <td className="auditRecord__before">
                                        <FieldValue value={field.before} />
                                    </td>
                                    <td className="auditRecord__after">
                                        <FieldValue value={field.after} />
                                    </td>
                                </>
                            ) : (
                                <td>
                                    <FieldValue value={record.event === "deleted" ? field.before : field.after} />
                                </td>
                            )}
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}

/**
 * Detalhe de um registro: linguagem simples por padrão e, pelo alternador, o log técnico cru.
 */
export default function AuditRecordModal({
    record,
    onClose,
}: {
    record: AuditRecord | null;
    onClose: () => void;
}): React.JSX.Element {
    const [view, setView] = useState<AuditView>("plain");
    const recordId = record?.id;

    // Cada registro abre na linguagem simples.
    useEffect(() => {
        if (recordId !== undefined) setView("plain");
    }, [recordId]);

    return (
        <Modal
            open={record !== null}
            title={record?.plain.summary ?? "Registro de auditoria"}
            size="large"
            effect="fadein"
            contentClassName="auditRecordModal"
            onClose={onClose}
            footer={(
                <div className="auditRecord__footer">
                    <Button
                        tone="cancel"
                        label="Fechar"
                        aria-label="Fechar detalhes do registro"
                        onClick={onClose}
                    />
                </div>
            )}
        >
            {record ? (
                <div className="auditRecord">
                    <div className="auditRecord__meta">
                        <div className="auditRecord__badges">
                            <TableLabel tone={auditEventTone(record.event)}>{record.plain.event}</TableLabel>
                            <TableLabel tone="inverse">{record.plain.type}</TableLabel>
                            <time dateTime={record.date ?? undefined}>{auditDateTime(record.date)}</time>
                        </div>
                        <div className="auditRecord__views" role="group" aria-label="Forma de exibição">
                            <Button
                                size="sm"
                                tone="inverse"
                                className="auditRecord__view"
                                leftIconClass="fa fa-align-left"
                                label="Formatado"
                                aria-pressed={view === "plain"}
                                onClick={() => setView("plain")}
                            />
                            <Button
                                size="sm"
                                tone="inverse"
                                className="auditRecord__view"
                                leftIconClass="fa fa-code"
                                label="Log técnico"
                                aria-pressed={view === "technical"}
                                onClick={() => setView("technical")}
                            />
                        </div>
                    </div>
                    {view === "plain" ? (
                        <PlainFields record={record} />
                    ) : (
                        <pre className="auditRecord__technical" tabIndex={0} aria-label="Log técnico do registro">
                            {JSON.stringify(record.technical, null, 2)}
                        </pre>
                    )}
                </div>
            ) : null}
        </Modal>
    );
}
