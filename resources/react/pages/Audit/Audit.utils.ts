import type { AuditEventValue } from "./Audit.types";

const dateTimeFormat = new Intl.DateTimeFormat("pt-BR", {
    dateStyle: "short",
    timeStyle: "short",
});

export function auditDateTime(value: string | null): string {
    if (!value) return "Não informado";

    const date = new Date(value);

    return Number.isNaN(date.getTime()) ? "Não informado" : dateTimeFormat.format(date);
}

export function auditEventTone(event: AuditEventValue): string {
    switch (event) {
        case "created":
            return "success";
        case "deleted":
            return "danger";
        case "restored":
            return "info";
        default:
            return "warning";
    }
}

/** Data completa (AAAA-MM-DD) ou vazia: só elas vão ao filtro, para não recarregar a cada dígito. */
export function isFilterDate(value: string): boolean {
    return value === "" || /^\d{4}-\d{2}-\d{2}$/.test(value);
}
