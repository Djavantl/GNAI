export interface AccessibilityFeatureRecord {
    id: number;
    name: string;
    description: string | null;
    is_active: boolean;
    materials_count?: number;
}

export interface AccessibilityFeaturePaginator {
    data: AccessibilityFeatureRecord[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
}
