import type {
    InspectionEvidence,
} from "@/components/Inspections/InspectionEvidenceCarousel/InspectionEvidenceCarousel.types";

export type InspectionDetailsData = {
    date: string;
    stateLabel: string;
    typeLabel: string;
    descriptionHtml: string;
    evidences: InspectionEvidence[];
};

export type InspectionDetailsSubject = {
    name: string;
    href: string;
};

export type InspectionDetailsCollection = {
    label: string;
    href: string;
};
