import type { PedagogicalRecordFormRecord } from "./PedagogicalRecordForm.types";

export type PedagogicalRecordShowRecord = Omit<PedagogicalRecordFormRecord, "guardians"> & {
    has_guardians: boolean;
    guardian_names: string;
};

export interface PedagogicalRecordShowSession {
    id: number;
    session_date: string | null;
    start_time: string | null;
    end_time: string | null;
    student_name: string | null;
    professional_name: string | null;
}

export interface PedagogicalRecordShowAbilities {
    update: boolean;
    delete: boolean;
    pdf: boolean;
}

export interface PedagogicalRecordShowProps {
    pedagogicalRecord: PedagogicalRecordShowRecord;
    session: PedagogicalRecordShowSession;
    can: PedagogicalRecordShowAbilities;
    returnStudentId?: number;
}
