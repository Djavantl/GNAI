export interface StudentDeficiencyStudent {
    id: number;
    person: { name: string };
}

export interface StudentDeficiencyRecord {
    id: number;
    deficiency_id: number;
    severity: string | null;
    notes: string | null;
    deficiency: { id: number; name: string } | null;
}

export interface DeficiencyOption {
    id: number;
    name: string;
}

export type StudentDeficiencyListRecord = StudentDeficiencyRecord & {
    severity_label: string | null;
    severity_color: string | null;
};

export type StudentDeficiencyShowRecord = StudentDeficiencyListRecord & {
    created_at: string | null;
};

export interface StudentDeficiencyPaginator {
    data: StudentDeficiencyListRecord[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
}

export type StudentDeficiencyFilters = {
    deficiency_id: string;
    severity: string;
};
