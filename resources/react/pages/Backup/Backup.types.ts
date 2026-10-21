export interface BackupRecord {
    id: number;
    file_name: string;
    file_path: string;
    size: string;
    status: "success" | "failed" | "archived" | null;
    created_at: string;
    created_at_label: string;
    user: { name: string } | null;
}

export interface BackupPaginator {
    data: BackupRecord[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
}

export interface BackupFilters {
    name: string;
    status: string;
    user_id: string;
}
