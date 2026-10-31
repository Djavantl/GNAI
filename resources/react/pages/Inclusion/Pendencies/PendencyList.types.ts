export type PendencyListVariant = "all" | "my";

export interface PendencyListOption {
    value: string;
    label: string;
}

export interface PendencyPriorityOption extends PendencyListOption {
    color: string;
}

export interface PendencyListRecord {
    description: string | null;
    created_at: string | null;
    updated_at: string | null;
    creator: { name: string } | null;
    id: number;
    title: string;
    priority: string | null;
    due_date: string | null;
    is_completed: boolean;
    assigned_professional?: {
        id: number;
        person?: {
            name?: string | null;
        } | null;
    } | null;
}

export interface PendencyPaginator {
    data: PendencyListRecord[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
}

export type PendencyListFilters = {
    title: string;
    assigned_to: string;
    priority: string;
    is_completed: string;
};

export interface PendencyListPermissions {
    create?: boolean;
    view: boolean;
    delete?: boolean;
    my?: boolean;
}

export interface PendencyListProps {
    pendencies: PendencyPaginator;
    priorities: PendencyPriorityOption[];
    professionals?: PendencyListOption[];
    filters: Partial<PendencyListFilters>;
    can: PendencyListPermissions;
}
