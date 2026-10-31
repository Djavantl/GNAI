export type AuditEventValue = "created" | "updated" | "deleted" | "restored";

export type AuditView = "plain" | "technical";

export type AuditField = {
    field: string;
    label: string;
    format: string;
    before: string | null;
    after: string | null;
    added: string[] | null;
    removed: string[] | null;
};

export type AuditRecord = {
    id: number;
    date: string | null;
    event: AuditEventValue;
    plain: {
        summary: string;
        author: string;
        type: string;
        event: string;
        fields: AuditField[];
    };
    technical: Record<string, unknown>;
};

export type AuditPaginator = {
    data: AuditRecord[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
};

export type AuditFilters = {
    date_from: string;
    date_to: string;
    actor: string;
    type: string;
    event: string;
    search: string;
};

export type AuditOption = {
    value: string;
    label: string;
};

export type AuditOptions = {
    actors: AuditOption[];
    types: AuditOption[];
    events: AuditOption[];
};
