export type DeficiencyOption = { id: number; name: string };
export type TechnologyRecord = {
    id: number;
    name: string;
    is_digital: boolean;
    is_loanable: boolean;
    quantity: number | null;
    asset_code: string | null;
    conservation_state: string | null;
    status: string | null;
    notes: string | null;
    is_active: boolean;
    deficiencies: DeficiencyOption[];
};
export type TechnologyFormOptions = {
    deficiencies: DeficiencyOption[];
    resourceStatuses: Record<string, string>;
    conservationStates: Record<string, string>;
    inspectionTypes: Record<string, string>;
    defaultInspection: string;
    defaultStatus?: string;
    can: { submit: boolean };
};
