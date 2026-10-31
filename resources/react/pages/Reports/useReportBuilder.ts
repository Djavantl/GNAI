import axios from 'axios';
import { useEffect, useMemo, useRef, useState } from 'react';
import { notify } from '@/components/UI/NotificationToast/notify';
import { copyReport, reportFailure } from './Reports.utils';
import type { ReportFilter, ReportMetadata, ReportPayload, ReportPermissions, ReportResult } from './Reports.types';

const http = axios.create({ headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });

export default function useReportBuilder(can: ReportPermissions) {
    const [sources, setSources] = useState<{ key: string; label: string }[]>([]);
    const [source, setSource] = useState('');
    const [metadata, setMetadata] = useState<ReportMetadata | null>(null);
    const [loadingSources, setLoadingSources] = useState(false);
    const [loadingMetadata, setLoadingMetadata] = useState(false);
    const [retrySources, setRetrySources] = useState(0);
    const [retryMetadata, setRetryMetadata] = useState(0);
    const [columns, setColumns] = useState<string[]>([]);
    const [relations, setRelations] = useState<string[]>([]);
    const [filters, setFilters] = useState<ReportFilter[]>([]);
    const [relatedOnly, setRelatedOnly] = useState(false);
    const [result, setResult] = useState<ReportResult | null>(null);
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [operation, setOperation] = useState<'run' | 'pdf' | 'copy' | null>(null);
    const sequence = useRef(0);
    const busy = useRef(false);
    const mounted = useRef(true);
    const request = useRef<AbortController | null>(null);
    const urls = useRef(new Set<string>());
    const downloadTimers = useRef(new Set<number>());

    useEffect(() => {
        mounted.current = true;
        return () => {
            mounted.current = false;
            request.current?.abort();
            urls.current.forEach(url => URL.revokeObjectURL(url));
            urls.current.clear();
            downloadTimers.current.forEach(timer => window.clearTimeout(timer));
            downloadTimers.current.clear();
        };
    }, []);

    async function showFailure(error: unknown, remap?: number[]): Promise<void> {
        const failure = await reportFailure(error);
        if (!mounted.current) return;
        const mapped: Record<string, string> = {};
        for (const [key, message] of Object.entries(failure.errors)) {
            mapped[key.replace(/^filters\.(\d+)/, (_, index: string) => `filters.${remap?.[Number(index)] ?? index}`)] = message;
        }
        setErrors(mapped);
        notify({ tone: 'error', title: 'Não foi possível concluir', text: failure.message });
    }

    useEffect(() => {
        if (!can.sources) return;
        const controller = new AbortController();
        setLoadingSources(true);
        setErrors(current => {
            const next = { ...current };
            delete next.source;
            return next;
        });
        void http.get<{ key: string; label: string }[]>('/reports/sources', { signal: controller.signal })
            .then(({ data }) => {
                if (!Array.isArray(data)) throw new Error('Resposta de assuntos inválida');
                if (!controller.signal.aborted) setSources(data);
            })
            .catch(error => {
                if (!controller.signal.aborted) {
                    setErrors(current => ({ ...current, source: 'Não foi possível carregar os assuntos. Tente novamente.' }));
                    void reportFailure(error).then(failure => {
                        if (!controller.signal.aborted) notify({ tone: 'error', title: 'Falha ao carregar assuntos', text: failure.message });
                    });
                }
            })
            .finally(() => { if (!controller.signal.aborted) setLoadingSources(false); });
        return () => controller.abort();
    }, [can.sources, retrySources]);

    useEffect(() => {
        setMetadata(null);
        setLoadingMetadata(false);
        setColumns([]);
        setRelations([]);
        setFilters([]);
        setRelatedOnly(false);
        setResult(null);
        setErrors({});
        if (!source || !can.metadata) return;
        const controller = new AbortController();
        setLoadingMetadata(true);
        void http.get<ReportMetadata>('/reports/metadata', { params: { source }, signal: controller.signal })
            .then(({ data }) => {
                if (!data || typeof data.key !== 'string' || !data.columns || !Array.isArray(data.filters) || !Array.isArray(data.relations)) {
                    throw new Error('Resposta de informações inválida');
                }
                if (!controller.signal.aborted) setMetadata(data);
            })
            .catch(error => {
                if (!controller.signal.aborted) {
                    setErrors({ source: 'Não foi possível carregar as informações deste assunto. Tente novamente.' });
                    void reportFailure(error).then(failure => {
                        if (!controller.signal.aborted) notify({ tone: 'error', title: 'Falha ao carregar informações', text: failure.message });
                    });
                }
            })
            .finally(() => { if (!controller.signal.aborted) setLoadingMetadata(false); });
        return () => controller.abort();
    }, [source, can.metadata, retryMetadata]);

    const addedRelations = useMemo(() => relations.flatMap(name => metadata?.relations.filter(relation => relation.name === name) ?? []), [metadata, relations]);
    const definitions = useMemo(() => [
        ...(metadata?.filters ?? []),
        ...addedRelations.flatMap(relation => relation.filter_definitions.map(definition => ({
            ...definition, key: `${relation.name}.${definition.key}`, label: `${relation.label} › ${definition.label}`,
        }))),
    ], [metadata, addedRelations]);

    function changed(): void { setResult(null); setErrors({}); }
    function validation(field: string, message: string): void {
        setErrors(current => ({ ...current, [field]: message }));
        notify({ tone: 'error', title: 'Confira o relatório', text: message });
    }
    function toggleColumn(key: string, checked: boolean): void {
        if (checked && columns.length >= 8) {
            validation('columns', 'Selecione no máximo 8 informações.');
            return;
        }
        changed();
        setColumns(current => checked ? [...current, key] : current.filter(column => column !== key));
    }
    function removeRelation(name: string): void {
        changed();
        setRelations(current => current.filter(relation => relation !== name));
        setColumns(current => current.filter(column => !column.startsWith(`${name}.`)));
        setFilters(current => current.filter(filter => !filter.field.startsWith(`${name}.`)));
    }
    function addFilter(): void {
        const definition = definitions[0];
        if (!definition || filters.length >= 12) return;
        changed();
        setFilters(current => [...current, {
            id: ++sequence.current, field: definition.key, operator: definition.operators[0],
            value: Object.keys(definition.options ?? {})[0] ?? '',
        }]);
    }
    function updateFilter(id: number, update: Partial<ReportFilter>): void {
        changed();
        setFilters(current => current.map(filter => {
            if (filter.id !== id) return filter;
            const definition = update.field ? definitions.find(item => item.key === update.field) : undefined;
            return definition ? {
                ...filter, field: definition.key, operator: definition.operators[0],
                value: Object.keys(definition.options ?? {})[0] ?? '',
            } : { ...filter, ...update };
        }));
    }

    async function perform(kind: 'run' | 'pdf'): Promise<void> {
        if (busy.current || (kind === 'run' ? !can.run : !can.pdf)) return;
        if (!metadata) { validation('source', 'Escolha o assunto do relatório.'); return; }
        if (!columns.length) { validation('columns', 'Marque pelo menos uma informação.'); return; }
        const included = filters.map((filter, index) => ({ filter, index })).filter(({ filter }) => filter.value !== '');
        const payload: ReportPayload = {
            source: metadata.key, columns, limit: 200, filter_related_values: relatedOnly,
            filters: included.map(({ filter }) => ({ field: filter.field, operator: filter.operator, value: filter.value })),
        };
        busy.current = true;
        setOperation(kind);
        setErrors({});
        const controller = new AbortController();
        request.current = controller;
        if (kind === 'run') setResult(null);
        try {
            if (kind === 'run') {
                const { data } = await http.post<ReportResult>('/reports/run', payload, { signal: controller.signal });
                if (!data?.headers || !Array.isArray(data.rows)) throw new Error('Resposta de relatório inválida');
                if (mounted.current) setResult(data);
            } else {
                const { data, headers } = await http.post<Blob>('/reports/export/pdf', payload, { responseType: 'blob', signal: controller.signal });
                if (!mounted.current) return;
                if (!String(headers['content-type']).includes('application/pdf')) throw new Error('Resposta PDF inválida');
                const url = URL.createObjectURL(data);
                urls.current.add(url);
                const link = document.createElement('a');
                link.href = url;
                link.download = /filename="?([^";]+)/i.exec(String(headers['content-disposition']))?.[1] ?? 'relatorio.pdf';
                document.body.appendChild(link);
                link.click();
                link.remove();
                const timer = window.setTimeout(() => {
                    URL.revokeObjectURL(url);
                    urls.current.delete(url);
                    downloadTimers.current.delete(timer);
                }, 60000);
                downloadTimers.current.add(timer);
                notify({ tone: 'success', title: 'PDF gerado', text: 'O download do relatório foi iniciado.' });
            }
        } catch (error) {
            if (!controller.signal.aborted) await showFailure(error, included.map(item => item.index));
        } finally {
            busy.current = false;
            if (mounted.current) setOperation(null);
        }
    }
    async function copy(): Promise<void> {
        if (!result || busy.current) return;
        busy.current = true;
        setOperation('copy');
        try {
            await copyReport(result);
            if (mounted.current) notify({ tone: 'success', title: 'Tabela copiada', text: 'Tabela copiada para a área de transferência.' });
        } catch {
            if (mounted.current) notify({ tone: 'error', title: 'Não foi possível copiar', text: 'Verifique a permissão da área de transferência e tente novamente.' });
        } finally {
            busy.current = false;
            if (mounted.current) setOperation(null);
        }
    }
    return {
        sources, source, setSource, metadata, loadingSources, loadingMetadata,
        retry: () => source ? setRetryMetadata(value => value + 1) : setRetrySources(value => value + 1),
        columns, toggleColumn, addedRelations,
        addRelation: (name: string) => { changed(); setRelations(current => current.includes(name) ? current : [...current, name]); },
        removeRelation, filters, definitions, addFilter, updateFilter,
        removeFilter: (id: number) => { changed(); setFilters(current => current.filter(filter => filter.id !== id)); },
        relatedOnly, setRelatedOnly: (value: boolean) => { changed(); setRelatedOnly(value); },
        result, errors, operation, perform, copy,
    };
}
