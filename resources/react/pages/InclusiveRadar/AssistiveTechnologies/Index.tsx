import { Head, Link, router, usePage } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';
import Pagination from '@/components/Navigation/Pagination/Pagination';
import Button from '@/components/UI/Button/Button';
import Table from '@/components/Tables/Table/Table';
import TableLabel from '@/components/UI/TableLabel/TableLabel';
import TextInput from '@/components/Forms/TextInput/TextInput';
import SelectField from '@/components/Forms/SelectField/SelectField';
import FormField from '@/components/Forms/FormField/FormField';
import InlineAlert from '@/components/UI/InlineAlert/InlineAlert';
import './Index.css';

type Filters = { name: string; is_digital: string; is_active: string; available: string };
type Technology = { id: number; name: string; is_digital: boolean; is_active: boolean; quantity: number | null; quantity_available: number | null };
type Props = {
  assistiveTechnologies: { data: Technology[]; current_page: number; last_page: number; total: number; from: number | null; to: number | null; prev_page_url: string | null; next_page_url: string | null };
  filters: Partial<Filters>;
  can: { create: boolean; show: boolean; destroy: boolean };
};
const base = '/inclusive-radar/assistive-technologies';
const emptyFilters: Filters = { name: '', is_digital: '', is_active: '', available: '' };
const selects = [
  { name: 'is_digital', label: 'Natureza', yes: 'Digital', no: 'Físico' },
  { name: 'is_active', label: 'Status', yes: 'Ativo', no: 'Inativo' },
  { name: 'available', label: 'Disponibilidade', yes: 'Disponível', no: 'Indisponível' },
] as const;

export default function InclusiveRadarAssistiveTechnologiesIndexPage({ assistiveTechnologies: records, filters: initialFilters, can }: Props): React.JSX.Element {
  const { errors } = usePage().props;
  const [filters, setFilters] = useState<Filters>({ ...emptyFilters, ...initialFilters });
  const [busy, setBusy] = useState(false);
  const [failure, setFailure] = useState('');
  const pending = useRef(false);
  const timer = useRef<ReturnType<typeof setTimeout> | null>(null);
  useEffect(() => { setFilters({ ...emptyFilters, ...initialFilters }); }, [initialFilters]);
  useEffect(() => () => { if (timer.current) clearTimeout(timer.current); }, []);

  function visit(values: Filters, url = base): void {
    if (pending.current) return;
    pending.current = true;
    setBusy(true);
    setFailure('');
    router.get(url, url === base ? Object.fromEntries(Object.entries(values).filter(([, value]) => value !== '')) : {}, {
      preserveState: true, preserveScroll: true,
      onError: () => setFailure('Não foi possível filtrar as tecnologias. Confira os campos e tente novamente.'),
      onHttpException: () => { setFailure('Não foi possível concluir a operação. Tente novamente.'); return false; },
      onNetworkError: () => { setFailure('Falha de conexão. Confira sua conexão e tente novamente.'); return false; },
      onFinish: () => { pending.current = false; setBusy(false); },
    });
  }
  function change(name: keyof Filters, value: string): void {
    const next = { ...filters, [name]: value };
    setFilters(next);
    if (timer.current) clearTimeout(timer.current);
    timer.current = setTimeout(() => visit(next), 500);
  }
  function remove(technology: Technology): void {
    if (pending.current || !window.confirm(`Excluir Tecnologia Assistiva\n\nA tecnologia ${technology.name} será removida do sistema.\n\nConfirmar exclusão?`)) return;
    if (timer.current) clearTimeout(timer.current);
    pending.current = true;
    setBusy(true);
    setFailure('');
    router.delete(`${base}/${technology.id}`, {
      preserveScroll: true,
      onError: () => setFailure('Não foi possível excluir a tecnologia. Tente novamente.'),
      onHttpException: () => { setFailure('Não foi possível concluir a operação. Tente novamente.'); return false; },
      onNetworkError: () => { setFailure('Falha de conexão. Confira sua conexão e tente novamente.'); return false; },
      onFinish: () => { pending.current = false; setBusy(false); },
    });
  }

  return <main className="technologyIndex">
    <Head title="Tecnologias Assistivas" />
    <nav aria-label="Navegação estrutural"><Link href="/auth/dashboard">Início</Link><span aria-hidden="true"> / </span><span aria-current="page">Tecnologias Assistivas</span></nav>
    <header className="technologyIndex__header">
      <div><h1>Tecnologias Assistivas</h1><p>Gerenciamento de periféricos, softwares e equipamentos de acessibilidade.</p></div>
      {can.create && <Button href={`${base}/create`} tone="primary">Adicionar Tecnologia Assistiva</Button>}
    </header>
    <section className="technologyIndex__card" aria-label="Listagem de tecnologias assistivas" aria-busy={busy}>
      <form className="technologyIndex__filters" onSubmit={event => { event.preventDefault(); if (timer.current) clearTimeout(timer.current); visit(filters); }}>
        <FormField id="technology-name" label="Nome" error={errors.name}>
          <TextInput id="technology-name" name="name" value={filters.name} maxLength={255} placeholder="Filtrar por nome..." disabled={busy} onChange={event => change('name', event.target.value)} invalid={!!errors.name} aria-describedby={errors.name ? 'technology-name-error' : undefined} />
        </FormField>
        {selects.map(field => <FormField key={field.name} id={field.name} label={field.label} error={errors[field.name]}>
          <SelectField id={field.name} name={field.name} label={field.label} placeholder="Todos"
            options={[{ value: '1', label: field.yes }, { value: '0', label: field.no }]}
            value={filters[field.name]} disabled={busy} onChange={event => change(field.name, event.target.value)}
            aria-invalid={!!errors[field.name]} aria-describedby={errors[field.name] ? `${field.name}-error` : undefined} />
        </FormField>)}
        <div className="technologyIndex__actions"><Button type="submit" tone="primary" disabled={busy}>Filtrar</Button><Button disabled={busy} onClick={() => { if (timer.current) clearTimeout(timer.current); setFilters(emptyFilters); visit(emptyFilters); }}>Limpar</Button></div>
      </form>
      <div role="status" className="technologyIndex__progress">{busy ? 'Processando…' : `${records.total} tecnologia(s) encontrada(s)`}</div>
      {failure && <div role="alert"><InlineAlert variant="danger">{failure}</InlineAlert></div>}
      <div className="technologyIndex__scroll" tabIndex={0} role="region" aria-label="Tabela de tecnologias assistivas">
        <Table bordered condensed className="technologyIndex__table">
          <thead><tr>{['Nome', 'Natureza', 'Estoque', 'Status', 'Ações'].map(label => <th key={label} scope="col">{label}</th>)}</tr></thead>
          <tbody>{records.data.map(technology => {
            const exhausted = !technology.is_digital && (technology.quantity_available ?? 0) <= 0;
            return <tr key={technology.id}>
              <td>{technology.name}</td><td>{technology.is_digital ? 'Digital' : 'Físico'}</td>
              <td>{technology.is_digital ? 'Ilimitado' : `${technology.quantity_available ?? 0} / ${technology.quantity ?? 0}`}</td>
              <td><TableLabel tone={exhausted ? 'danger' : technology.is_active ? 'success' : 'inverse'}>{exhausted ? 'Esgotado' : technology.is_active ? 'Ativo' : 'Inativo'}</TableLabel></td>
              <td><div className="technologyIndex__actions">
                {can.show && <Button href={`${base}/${technology.id}`} size="sm" tone="dark-blue" iconOnly leftIconClass="fa fa-eye" aria-label={`Ver ${technology.name}`} />}
                {can.destroy && <Button size="sm" tone="dark-red" iconOnly leftIconClass="fa fa-trash-o" disabled={busy} aria-label={`Excluir ${technology.name}`} onClick={() => remove(technology)} />}
                {!can.show && !can.destroy && <span>Nenhuma ação</span>}
              </div></td>
            </tr>;
          })}{!records.data.length && <tr><td colSpan={5} className="technologyIndex__empty">Nenhuma tecnologia cadastrada.</td></tr>}</tbody>
        </Table>
      </div>
      <footer className="technologyIndex__pagination">
        <span>{records.from ?? 0}–{records.to ?? 0} de {records.total}</span>
        <nav aria-label="Páginas da listagem">
          <Pagination currentPage={records.current_page} pageCount={records.last_page} disabled={busy} onSelect={page => {
            if (timer.current) clearTimeout(timer.current);
            const params = new URLSearchParams(window.location.search);
            params.set('page', String(page));
            visit(filters, `${base}?${params.toString()}`);
          }} />
        </nav>
      </footer>
    </section>
  </main>;
}
