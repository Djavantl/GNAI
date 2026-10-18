export type MaterialFilters = {
    name: string;
    is_digital: string;
    accessibility_feature_id: string;
    is_active: string;
    available: string;
    per_page: string;
};

export type AccessibleEducationalMaterial = {
    id: number;
    name: string;
    is_digital: boolean;
    is_active: boolean;
    quantity: number | null;
    quantity_available: number | null;
};

export type MaterialPaginator = {
    data: AccessibleEducationalMaterial[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
};

export type MaterialPermissions = {
    create: boolean;
    show: boolean;
    destroy: boolean;
};

export type MaterialIndexProps = {
    materials: MaterialPaginator;
    filters: Partial<MaterialFilters>;
    accessibilityFeatureOptions: Record<string, string>;
    can: MaterialPermissions;
};

export const materialsBase =
    '/inclusive-radar/accessible-educational-materials';

export const emptyMaterialFilters: MaterialFilters = {
    name: '',
    is_digital: '',
    accessibility_feature_id: '',
    is_active: '',
    available: '',
    per_page: '',
};
