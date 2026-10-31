export interface DisciplineRecord {
    id: number;
    name: string;
    description: string | null;
    is_active: boolean;
}

export type DisciplineDetailRecord = DisciplineRecord & {
    courses?: Array<{ id: number; name: string }>;
};

export type DisciplinePaginator = {
    data: DisciplineRecord[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
};

export type DisciplineFilters = {
    name: string;
    is_active: string;
};
