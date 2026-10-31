import type { SelectFieldOption } from "@/components/Forms/SelectField/SelectField.types";
import type { SessionOptionMap, SessionPersonOption } from "./SessionForm.types";

export const sessionsBase = "/specialized-educational-support/sessions";

export function sessionShowUrl(id: number): string {
    return `${sessionsBase}/${id}/show`;
}

export function mapOptions(options: SessionOptionMap): SelectFieldOption[] {
    return Object.entries(options).map(([value, label]) => ({
        value,
        label,
    }));
}

export function personName(item?: SessionPersonOption | null): string {
    return item?.person?.name ?? "Sem Nome";
}

/**
 * Mesma regra do legado: início pela manhã termina até 12:00; início à tarde
 * termina a partir de 14:00. O fim sempre é posterior ao início.
 */
export function endTimeOptions(
    endTimes: SessionOptionMap,
    startTime: string,
): SelectFieldOption[] {
    const options = mapOptions(endTimes);

    if (!startTime) return options;

    const isMorning = Number(startTime.split(":")[0]) < 12;

    return options.filter(({ value }) => {
        const [hour, minutes] = value.split(":").map(Number);

        if (value <= startTime) return false;

        return isMorning
            ? hour < 12 || (hour === 12 && minutes === 0)
            : hour >= 14;
    });
}

export function adjustEndTime(
    endTimes: SessionOptionMap,
    startTime: string,
    endTime: string,
): string {
    if (!startTime || !endTime) return endTime;

    const visible = endTimeOptions(endTimes, startTime);

    if (visible.some(option => option.value === endTime)) return endTime;

    return visible[0]?.value ?? endTime;
}

export function localToday(): string {
    const today = new Date();

    return [
        today.getFullYear(),
        String(today.getMonth() + 1).padStart(2, "0"),
        String(today.getDate()).padStart(2, "0"),
    ].join("-");
}
