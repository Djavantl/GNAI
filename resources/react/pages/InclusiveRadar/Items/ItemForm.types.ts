import type { ItemType } from './Items.types';

export type NamedOption = { id: number; name: string };

export type ItemRecord = {
    id: number;
    item_type: ItemType;
    name: string;
    is_digital: boolean;
    is_loanable: boolean;
    quantity: number | null;
    asset_code: string | null;
    conservation_state: string | null;
    status: string | null;
    notes: string | null;
    is_active: boolean;
    deficiencies: NamedOption[];
    accessibility_features: NamedOption[];
};

export type ItemFormOptions = {
    itemTypes: Record<string, string>;
    deficiencies: NamedOption[];
    accessibilityFeatures: NamedOption[];
    resourceStatuses: Record<string, string>;
    conservationStates: Record<string, string>;
    inspectionTypes: Record<string, string>;
    defaultInspection: string;
    defaultStatus?: string;
    can: { submit: boolean };
};
