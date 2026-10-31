export interface ProfessionalPerson {
    id: number;
    name: string;
    document: string | null;
    birth_date: string | null;
    gender: string | null;
    email: string | null;
    phone: string | null;
    address: string | null;
    photo: string | null;
    photo_url?: string | null;
}

export interface ProfessionalRecord {
    id: number;
    person: ProfessionalPerson;
    position_id: number | null;
    registration: string;
    entry_date: string | null;
    status: string | null;
    show_on_website?: boolean;
    user?: { id: number; is_admin: boolean } | null;
}

export interface ProfessionalFormOptions {
    positions: Array<{ id: number; name: string }>;
    genders: Record<string, string>;
    professionalStatuses: Record<string, string>;
    defaultGender?: string;
    defaultStatus?: string;
    canManageAdministrators: boolean;
}

export interface ProfessionalListRecord {
    entry_date: string | null;
    id: number;
    person: Pick<ProfessionalPerson, "id" | "name" | "email" | "phone"> & { photo_url?: string | null };
    position: { id: number; name: string } | null;
    registration: string;
    status: string | null;
    show_on_website?: boolean;
}

export type ProfessionalFilters = {
    name: string;
    email: string;
    registration: string;
    position: string;
    status: string;
};

export interface ProfessionalPaginator {
    data: ProfessionalListRecord[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
}

export interface ProfessionalIndexProps {
    professionals: ProfessionalPaginator;
    positions: Array<{ id: number; name: string }>;
    professionalStatuses: Record<string, string>;
    professionalStatusColors: Record<string, string>;
    filters: Partial<ProfessionalFilters>;
    can: { create: boolean; view: boolean; delete: boolean };
}

export interface ProfessionalShowProps {
    professional: ProfessionalRecord & {
        position: { id: number; name: string } | null;
    };
    display: {
        statusLabel: string | null;
        statusColor: string | null;
        serviceTime: string | null;
    };
    impersonationUserId: number | null;
    can: { update: boolean; delete: boolean };
}
