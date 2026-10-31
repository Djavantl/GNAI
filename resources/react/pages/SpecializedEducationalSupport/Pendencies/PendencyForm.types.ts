export interface PendencyProfessionalOption {
    id: number;
    person?: {
        name?: string | null;
    } | null;
}

export interface PendencyFormOptions {
    professionals: PendencyProfessionalOption[];
    priorities: Record<string, string>;
}

export interface PendencyRecord {
    id: number;
    title: string;
    description: string | null;
    assigned_to: number | null;
    priority: string | null;
}

export type PendencyFormData = {
    title: string;
    description: string;
    assigned_to: string;
    priority: string;
    due_date: string;
};
