/** Restringe cores editáveis ao mesmo formato aceito pelo backend. */
export function appearanceColor(value: string, fallback: string): string {
    return /^#[0-9a-f]{6}$/i.test(value) ? value : fallback;
}
