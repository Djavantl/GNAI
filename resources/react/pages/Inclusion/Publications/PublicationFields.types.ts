import type { PublicationFormData } from "./Publication.types";

export type PublicationFieldsProps = {
    data: PublicationFormData;
    errors: Record<string, string | undefined>;
    disabled: boolean;
    update: <K extends keyof PublicationFormData>(key: K, value: PublicationFormData[K]) => void;
    updateWith: (callback: (data: PublicationFormData) => PublicationFormData) => void;
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
