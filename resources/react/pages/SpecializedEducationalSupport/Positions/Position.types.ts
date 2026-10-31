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

export interface PositionFormProps {
    permissions: GroupedPositionPermissions;
}
