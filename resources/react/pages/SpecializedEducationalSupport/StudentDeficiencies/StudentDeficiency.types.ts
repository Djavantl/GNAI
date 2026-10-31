export interface StudentDeficiencyStudent {
    id: number;
    person: { name: string };
}

export interface StudentDeficiencyRecord {
    id: number;
    deficiency_id: number;
    severity: string | null;
    notes: string | null;
    deficiency: { id: number; name: string } | null;
}

export interface DeficiencyOption {
    id: number;
    name: string;
}
