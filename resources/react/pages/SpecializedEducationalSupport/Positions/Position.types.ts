export interface PositionPermission {
    id: number;
    name: string;
    slug: string;
}

export type GroupedPositionPermissions = Record<string, PositionPermission[]>;

export interface PositionRecord {
    id: number;
    name: string;
    description: string | null;
    permissions?: Array<Pick<PositionPermission, "id">>;
}

export type PositionDetailRecord = Omit<PositionRecord, "permissions"> & {
    is_active: boolean;
    permissions?: Array<Pick<PositionPermission, "id" | "name">>;
};

export interface PositionFormProps {
    permissions: GroupedPositionPermissions;
}

export type PositionListRecord = Pick<PositionRecord, "id" | "name" | "description"> & {
    is_active: boolean;
};

export type PositionPaginator = {
    data: PositionListRecord[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
};

export type PositionFilters = {
    name: string;
    description: string;
    is_active: string;
};
