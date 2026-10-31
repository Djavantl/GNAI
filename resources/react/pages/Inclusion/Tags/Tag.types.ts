export interface TagRecord {
    id: number;
    name: string;
    is_active: boolean;
    publications_count?: number;
}

export interface TagPaginator {
    data: TagRecord[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
}
