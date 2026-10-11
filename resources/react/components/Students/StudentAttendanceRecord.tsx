import Table from '@/components/Tables/Table/Table';
import Button from '@/components/UI/Button/Button';
import Badge from '@/components/UI/Badge/Badge';
import StudentSection from './StudentSection';
import type { StudentShowProps, AttendanceSession } from './Students.types';
import { aeeBase, dateLabel, studentUrl } from './Students.utils';

export default function StudentAttendanceRecord({ student, permissions, aeeEvaluations, pedagogicalRecords }: StudentShowProps): React.JSX.Element | null {
  const canAee = permissions['aee-record.view-all'] || permissions['aee-record.view-own'];
  const canPedagogical = permissions['pedagogical-record.view-all'] || permissions['pedagogical-record.view-own'];
  if (!canAee && !canPedagogical) return null;
  const sections = [
    { id: 'aee-records', title: 'Registros de Atendimentos AEE', view: permissions['aee-record.view'], manage: canAee,
      empty: 'Nenhum registro de atendimento AEE encontrado.',
      rows: aeeEvaluations.map(item => ({
        id: item.id, session: item.aee_record?.attendance_session, duration: item.aee_record?.duration,
        present: item.is_present, guardians: undefined, href: studentUrl(student.id, `aee-records/${item.id}/show`),
      })) },
    { id: 'pedagogical-records', title: 'Atendimentos Pedagógicos', view: permissions['pedagogical-record.view'], manage: canPedagogical,
      empty: 'Nenhum atendimento pedagógico encontrado.',
      rows: pedagogicalRecords.map(item => ({
        id: item.id, session: item.attendance_session, duration: item.duration,
        present: item.is_present, guardians: item.guardians.length > 0, href: `${aeeBase}/pedagogical-records/${item.id}/show`,
      })) },
  ];
  return <>{sections.map(section => <StudentSection key={section.id} id={section.id} title={section.title}
    actions={section.manage ? <>
      <small>São exibidos até 5 registros mais recentes. Para consultar todos, clique em “Gerenciar Registros”.</small>
      {section.view ? <>
        <Button href={studentUrl(student.id, `${section.id}/pdf`)} target="_blank" rel="noopener noreferrer">Histórico em PDF</Button>
        <Button href={studentUrl(student.id, section.id)} tone="warning">Gerenciar Registros</Button>
      </> : null}
    </> : undefined}>
    <div className="studentsPage__table" role="region" aria-label={section.title} tabIndex={0}><Table hover>
      <thead><tr>{['Data', 'Profissional', 'Duração', 'Presença', ...(section.id === 'pedagogical-records' ? ['Com responsáveis'] : []), 'Ações']
        .map(label => <th key={label} scope="col">{label}</th>)}</tr></thead>
      <tbody>{section.rows.length ? section.rows.map((row: {
        id: number; session?: AttendanceSession | null; duration?: number | string | null;
        present: boolean; guardians?: boolean; href: string;
      }) => <tr key={row.id}>
        <td>{dateLabel(row.session?.session_date)}</td><td>{row.session?.professional?.person?.name ?? 'Não informado'}</td>
        <td>{row.duration ?? '—'}</td><td><Badge variant={row.present ? 'success' : 'danger'}>{row.present ? 'Presente' : 'Ausente'}</Badge></td>
        {section.id === 'pedagogical-records' ? <td>{row.guardians ? 'Sim' : 'Não'}</td> : null}
        <td>{section.view ? <Button href={row.href} tone="info" aria-label={`Visualizar atendimento de ${dateLabel(row.session?.session_date)}`}>Ver</Button> : 'Nenhuma ação'}</td>
      </tr>) : <tr><td colSpan={section.id === 'pedagogical-records' ? 6 : 5} className="studentsPage__empty">{section.empty}</td></tr>}</tbody>
    </Table></div>
  </StudentSection>)}</>;
}
