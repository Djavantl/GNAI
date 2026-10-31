import type { InstitutionalEventFormData } from "./InstitutionalEvent.types";

export type PublicationFieldsProps = {
    data: InstitutionalEventFormData;
    errors: Record<string, string | undefined>;
    disabled: boolean;
    update: <K extends keyof InstitutionalEventFormData>(key: K, value: InstitutionalEventFormData[K]) => void;
    updateWith: (callback: (data: InstitutionalEventFormData) => InstitutionalEventFormData) => void;
};

export function describedBy(
    errors: Record<string, string | undefined>,
    errorKey: string,
    id: string,
    hint = false,
): string | undefined {
    return [hint ? `${id}-hint` : null, errors[errorKey] ? `${id}-error` : null]
        .filter(Boolean)
        .join(" ") || undefined;
}
