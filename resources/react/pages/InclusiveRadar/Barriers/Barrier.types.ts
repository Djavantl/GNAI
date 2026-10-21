import type { GeoMapReference } from "@/components/InclusiveRadar/GeoMap/GeoMap";

export type BarrierOption = { value: string; label: string };
export type BarrierInstitution = GeoMapReference & {
    locations: { id: number; name: string; label: string; latitude: number; longitude: number }[];
};
export type BarrierCategory = { id: number; name: string; blocks_map: boolean };
export type BarrierDeficiency = { id: number; name: string };
export type BarrierPaginator = {
    data: BarrierRecord[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
};
export type BarrierFilters = { name: string; category: string; priority: string; status: string };
export type BarrierInspection = {
    id: number;
    status: string | null;
    type: string | null;
    inspection_date: string;
    description: string | null;
    created_at: string;
    evidences?: BarrierEvidence[];
};
export type BarrierEvidence = {
    id: number;
    path: string;
    original_name: string | null;
    mime_type: string | null;
    size: number;
};
export type BarrierRecord = {
    id: number;
    name: string;
    description: string | null;
    institution_id: number;
    barrier_category_id: number;
    location_id: number | null;
    location_specific_details: string | null;
    affected_student_id?: number | string | null;
    affected_professional_id?: number | string | null;
    priority: string;
    identified_at: string;
    latitude: number | string | null;
    longitude: number | string | null;
    is_anonymous: boolean;
    not_applicable: boolean;
    affected_person_name: string | null;
    affected_person_role: string | null;
    is_active: boolean;
    category?: { id: number; name: string; blocks_map?: boolean } | null;
    institution?: { id: number; name: string; latitude?: number | string; longitude?: number | string; default_zoom?: number } | null;
    location?: { id: number; name: string } | null;
    priority_label?: string;
    status_label?: string;
    status?: string | null;
    deficiencies?: { id: number; name: string }[];
    inspections?: BarrierInspection[];
    affected_student?: { person?: { name: string } } | null;
    affected_professional?: { person?: { name: string } } | null;
    registered_by?: { name: string } | null;
};
