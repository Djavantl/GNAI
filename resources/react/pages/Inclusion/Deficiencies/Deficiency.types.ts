export interface DeficiencyRecord {
    id: number;
    name: string;
    cid_code: string | null;
    description: string | null;
    is_active: boolean;
}

export type DeficiencyPaginator = {
    data: DeficiencyRecord[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
};

export type DeficiencyFilters = {
    name: string;
    cid_code: string;
    is_active: string;
};
