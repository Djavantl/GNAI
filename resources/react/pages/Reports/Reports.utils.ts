import axios from 'axios';
import type { Operator, ReportResult } from './Reports.types';

export const operatorLabels: Record<Operator, string> = {
    eq: 'É igual a', neq: 'Diferente de', contains: 'Contém',
    gt: 'Maior que', gte: 'A partir de', lt: 'Menor que', lte: 'Até',
};
export const selectOptions = (values: Record<string, string>) =>
    Object.entries(values).map(([value, label]) => ({ value, label }));

export async function reportFailure(error: unknown): Promise<{ message: string; errors: Record<string, string> }> {
    if (!axios.isAxiosError(error)) {
        return { message: 'Não foi possível concluir a operação. Tente novamente.', errors: {} };
    }
    let data = error.response?.data;
    if (data instanceof Blob) {
        try { data = JSON.parse(await data.text()); } catch { data = null; }
    }
    const errors: Record<string, string> = {};
    for (const [field, messages] of Object.entries(data?.errors ?? {})) {
        errors[field] = Array.isArray(messages) ? String(messages[0]) : String(messages);
    }
    const status = error.response?.status;
    const fallback = !error.response ? 'Falha de conexão. Confira sua conexão e tente novamente.'
        : status === 401 || status === 419 ? 'Sua sessão expirou. Entre novamente no sistema.'
        : status === 403 ? 'Você não tem permissão para esta operação.'
        : 'Não foi possível concluir a operação. Tente novamente.';
    return {
        message: Object.keys(errors).length ? 'Confira os campos indicados no relatório.'
            : status && status >= 500 ? fallback : data?.message ?? data?.error ?? fallback,
        errors,
    };
}

export function reportCells(result: ReportResult): string[][] {
    const keys = Object.keys(result.headers);
    return result.rows.map(row => keys.map(key => String(row[key.replaceAll('.', '__')] ?? 'Não informado')));
}

export async function copyReport(result: ReportResult): Promise<void> {
    const rows = [Object.values(result.headers), ...reportCells(result)];
    const text = rows.map(row => row.join('\t')).join('\n');
    const escape = (value: string) => value.replace(/[&<>"']/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
    })[char]!);
    if (navigator.clipboard?.write && typeof ClipboardItem !== 'undefined') {
        const html = `<table><thead><tr>${rows[0].map(cell => `<th>${escape(cell)}</th>`).join('')}</tr></thead><tbody>${rows.slice(1).map(row => `<tr>${row.map(cell => `<td>${escape(cell)}</td>`).join('')}</tr>`).join('')}</tbody></table>`;
        try {
            await navigator.clipboard.write([new ClipboardItem({
                'text/plain': new Blob([text], { type: 'text/plain' }),
                'text/html': new Blob([html], { type: 'text/html' }),
            })]);
            return;
        } catch { /* Tenta a cópia de texto quando HTML não é permitido. */ }
    }
    if (navigator.clipboard?.writeText) {
        try { await navigator.clipboard.writeText(text); return; } catch { /* Fallback legado. */ }
    }
    const previous = document.activeElement;
    const input = document.createElement('textarea');
    input.value = text;
    input.readOnly = true;
    input.style.position = 'fixed';
    input.style.left = '-9999px';
    document.body.appendChild(input);
    try {
        input.select();
        if (!document.execCommand('copy')) throw new Error('Cópia indisponível');
    } finally {
        input.remove();
        if (previous instanceof HTMLElement) previous.focus();
    }
}
