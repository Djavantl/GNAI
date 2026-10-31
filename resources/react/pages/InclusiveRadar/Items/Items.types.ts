export type ItemFilters = {
    name: string;
    item_type: string;
    is_digital: string;
    accessibility_feature_id: string;
    is_active: string;
    available: string;
    per_page: string;
};

export type ItemType = 'assistive_technology' | 'accessible_educational_material';

export type ItemSummary = {
    id: number;
    item_type: ItemType;
    name: string;
    is_digital: boolean;
    is_active: boolean;
    quantity: number | null;
    quantity_available: number | null;
    asset_code: string | null;
    is_loanable: boolean;
    conservation_state_label: string | null;
    resource_status_label: string | null;
    deficiencies: Array<{ id: number; name: string }>;
    accessibility_features: Array<{ id: number; name: string }>;
    created_at: string | null;
    updated_at: string | null;
};

export type ItemPaginator = {
    data: ItemSummary[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
};

export type ItemPermissions = {
    create: boolean;
    show: boolean;
    destroy: boolean;
};

export type ItemIndexProps = {
    items: ItemPaginator;
    filters: Partial<ItemFilters>;
    itemTypeOptions: Record<string, string>;
    accessibilityFeatureOptions: Record<string, string>;
    can: ItemPermissions;
};

export const itemsBase = '/inclusive-radar/items';

export const itemTypeLabels: Record<ItemType, string> = {
    assistive_technology: 'Tecnologia Assistiva',
    accessible_educational_material: 'Material Pedagógico Acessível',
};

export const emptyItemFilters: ItemFilters = {
    name: '',
    item_type: '',
    is_digital: '',
    accessibility_feature_id: '',
    is_active: '',
    available: '',
    per_page: '',
};
