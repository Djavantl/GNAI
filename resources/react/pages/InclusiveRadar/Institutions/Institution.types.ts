import type { GeoPoint } from "@/components/InclusiveRadar/GeoMap/GeoMap";

export type InstitutionRecord = GeoPoint & {
    id: number;
    name: string;
    short_name: string | null;
    city: string;
    state: string;
    district: string | null;
    address: string | null;
    default_zoom: number;
    is_active: boolean;
};

export type InstitutionPaginator = {
    data: InstitutionRecord[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
};

export type InstitutionFilters = {
    name: string;
    location: string;
    is_active: string;
};
