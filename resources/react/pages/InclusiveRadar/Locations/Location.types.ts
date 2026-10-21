import type { GeoMapMarker, GeoMapReference, GeoPoint } from "@/components/InclusiveRadar/GeoMap/GeoMap";

export type LocationRecord = GeoPoint & {
    id: number;
    institution_id: number;
    name: string;
    type: string | null;
    description: string | null;
    is_active: boolean;
    institution?: { id: number; name: string; default_zoom?: number };
};

export type LocationPaginator = {
    data: LocationRecord[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
};

export type LocationFilters = {
    name: string;
    institution_name: string;
    is_active: string;
};

export type InstitutionOption = { value: string; label: string };
export type InstitutionMapData = GeoMapReference & { locations?: GeoMapMarker[] };
