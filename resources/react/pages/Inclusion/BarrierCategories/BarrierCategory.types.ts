export interface BarrierCategoryRecord {
    id: number;
    name: string;
    description: string | null;
    is_active: boolean;
    blocks_map: boolean;
    barriers_count?: number;
}

export interface BarrierCategoryPaginator {
    data: BarrierCategoryRecord[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
}
