import { useState } from 'react';
import { router } from '@inertiajs/react';
import Table from '@/components/Tables/Table/Table';
import Pagination from '@/components/Navigation/Pagination/Pagination';
import Button from '@/components/UI/Button/Button';
import Badge from '@/components/UI/Badge/Badge';
import InlineAlert from '@/components/UI/InlineAlert/InlineAlert';
import type { StudentIndexProps } from './Students.types';
import { dateLabel, studentsBase, studentUrl } from './Students.utils';
import { useStudentRequest } from './useStudentRequest';

export default function StudentList({ students, filters, permissions, studentStatuses, statusColors }: StudentIndexProps): React.JSX.Element {
  const [processing, setProcessing] = useState(false);
  const request = useStudentRequest();
  function pageHref(page: number) {
    const query = new URLSearchParams(Object.entries(filters).filter(([, value]) => value !== '').map(([key, value]) => [key, String(value)]));
    query.set('page', String(page));
    return `${studentsBase}?${query}`;
  }
  return <div aria-busy={processing}>
    {request.error ? <div role="alert"><InlineAlert variant="danger">{request.error}</InlineAlert></div> : null}
    <div className="studentsPage__table" tabIndex={0} role="region" aria-label="Lista de alunos">
      <Table hover>
        <thead><tr>{['Nome', 'Telefone', 'E-mail', 'Status', 'Ingresso', 'Ações'].map(label => <th key={label} scope="col">{label}</th>)}</tr></thead>
        <tbody>{students.data.length ? students.data.map(student => <tr key={student.id}>
          <td><div className="studentsPage__identity"><img src={student.person.photo_url} alt={`Foto de ${student.person.name}`} className="studentsPage__avatar" /><strong>{student.person.name}</strong></div></td>
          <td>{student.person.phone || 'Não informado'}</td>
          <td>{student.person.email || 'Não informado'}</td>
          <td><Badge variant={statusColors[student.status] ?? 'default'}>{studentStatuses[student.status] ?? student.status}</Badge></td>
          <td>{dateLabel(student.entry_date)}</td>
          <td>{permissions['student.view'] ? <Button href={studentUrl(student.id)} tone="info" aria-label={`Ver ficha de ${student.person.name}`}>Ver</Button> : 'Nenhuma ação'}</td>
        </tr>) : <tr><td colSpan={6} className="studentsPage__empty">Nenhum aluno encontrado.</td></tr>}</tbody>
      </Table>
    </div>
    <div className="studentsPage__pagination">
      <span>Mostrando {students.from ?? 0} - {students.to ?? 0} de {students.total}</span>
      {students.last_page > 1 ? <nav aria-label="Páginas de alunos"><Pagination currentPage={students.current_page} pageCount={students.last_page}
        disabled={processing} onSelect={page => {
          if (processing) return;
          router.get(pageHref(page), {}, { ...request.options, preserveState: true, preserveScroll: true,
            onStart: () => setProcessing(true), onFinish: () => setProcessing(false) });
        }} /></nav> : null}
      <span role="status">{processing ? 'Carregando página...' : ''}</span>
    </div>
  </div>;
}
