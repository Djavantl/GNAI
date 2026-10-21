import type { TabItem } from '@/components/Navigation/Tabs/Tabs';
import TableActions from '@/components/Tables/TableActions/TableActions';
import type { TableListColumn } from '@/components/Tables/TableList/TableList';
import Button from '@/components/UI/Button/Button';
import TableLabel from '@/components/UI/TableLabel/TableLabel';
import DeleteStudentRecord from './DeleteStudentRecord';
import StudentRecordTable from './StudentRecordTable';
import StudentSection from './StudentSection';
import type {
  AttendanceSession,
  Permissions,
  StudentShowProps,
} from './Students.types';
import { aeeBase, dateLabel, studentUrl } from './Students.utils';

interface AttendanceRow {
  id: number;
  session?: AttendanceSession | null;
  duration?: number | string | null;
  present: boolean;
  guardians?: boolean;
  canManage: boolean;
  href: string;
  editHref: string;
  deleteHref: string;
  pdfHref?: string;
}

interface AttendancePanelProps {
  id: 'aee-records' | 'pedagogical-records';
  title: string;
  iconClass: string;
  emptyMessage: string;
  rows: AttendanceRow[];
  studentId: number;
  permissions: Permissions;
}

const aeeColumns: TableListColumn[] = [
  {
    id: 'date',
    label: 'Data',
    width: '19%',
    minWidth: 90,
    fixed: 'start',
  },
  { id: 'professional', label: 'Profissional', width: '28%', minWidth: 120 },
  { id: 'duration', label: 'Duração', width: '15%', minWidth: 76 },
  { id: 'presence', label: 'Presença', width: '16%', minWidth: 82 },
  {
    id: 'actions',
    label: 'Ações',
    width: '22%',
    minWidth: 140,
    fixed: 'end',
  },
];

const pedagogicalColumns: TableListColumn[] = [
  {
    id: 'date',
    label: 'Data',
    width: '13%',
    minWidth: 86,
    fixed: 'start',
  },
  { id: 'time', label: 'Horário', width: '13%', minWidth: 84 },
  { id: 'professional', label: 'Profissional', width: '19%', minWidth: 110 },
  { id: 'duration', label: 'Duração', width: '11%', minWidth: 72 },
  { id: 'presence', label: 'Presença', width: '12%', minWidth: 80 },
  { id: 'guardians', label: 'Responsáveis', width: '12%', minWidth: 80 },
  {
    id: 'actions',
    label: 'Ações',
    width: '20%',
    minWidth: 176,
    fixed: 'end',
  },
];

function sessionTime(session?: AttendanceSession | null): string {
  if (!session?.start_time) return '---';
  const start = session.start_time.slice(0, 5);
  const end = session.end_time?.slice(0, 5);
  return end ? `${start} – ${end}` : start;
}

function AttendanceRowActions({
  row,
  isAee,
  permissions,
}: {
  row: AttendanceRow;
  isAee: boolean;
  permissions: Permissions;
}): React.JSX.Element | string {
  const prefix = isAee ? 'aee-record' : 'pedagogical-record';
  const canView = permissions[`${prefix}.view`];
  const canUpdate = permissions[`${prefix}.update`] && row.canManage;
  const canDelete = permissions[`${prefix}.delete`] && row.canManage;

  if (!canView && !canUpdate && !canDelete) return 'Nenhuma ação';

  return (
    <TableActions>
      {canView ? (
        <Button
          href={row.href}
          size="sm"
          tone="dark-blue"
          iconOnly
          leftIconClass="fa fa-eye"
          aria-label={`Visualizar atendimento de ${dateLabel(row.session?.session_date)}`}
        />
      ) : null}
      {canView && row.pdfHref ? (
        <Button
          href={row.pdfHref}
          target="_blank"
          rel="noopener noreferrer"
          size="sm"
          tone="cancel"
          iconOnly
          leftIconClass="fa fa-file-pdf-o"
          aria-label={`Gerar PDF do atendimento de ${dateLabel(row.session?.session_date)}`}
        />
      ) : null}
      {canUpdate ? (
        <Button
          href={row.editHref}
          size="sm"
          tone="warning"
          iconOnly
          leftIconClass="fa fa-pencil"
          aria-label={`Editar atendimento de ${dateLabel(row.session?.session_date)}`}
        />
      ) : null}
      {canDelete ? (
        <DeleteStudentRecord
          action={row.deleteHref}
          title={isAee
            ? 'Excluir registro do aluno'
            : 'Excluir atendimento pedagógico'}
          message={isAee
            ? 'Excluir apenas o registro deste aluno neste atendimento AEE?'
            : 'Excluir este atendimento pedagógico?'}
          ariaLabel={`Excluir atendimento de ${dateLabel(row.session?.session_date)}`}
        />
      ) : null}
    </TableActions>
  );
}

function AttendancePanel({
  id,
  title,
  iconClass,
  emptyMessage,
  rows,
  studentId,
  permissions,
}: AttendancePanelProps): React.JSX.Element {
  const isAee = id === 'aee-records';
  const columns = isAee ? aeeColumns : pedagogicalColumns;

  return (
    <StudentSection id={id} title={title} iconClass={iconClass}>
      <StudentRecordTable
        ariaLabel={title}
        columns={columns}
        count={rows.length}
        emptyMessage={emptyMessage}
        actions={permissions[`${isAee ? 'aee' : 'pedagogical'}-record.view`] ? (
          <Button
            href={studentUrl(studentId, `${id}/pdf`)}
            target="_blank"
            rel="noopener noreferrer"
            tone="dark-red"
            leftIconClass="fa fa-file-pdf-o"
          >
            Histórico em PDF
          </Button>
        ) : undefined}
      >
        {rows.map((row) => (
          <tr key={row.id}>
            <td><strong>{dateLabel(row.session?.session_date)}</strong></td>
            {!isAee ? <td>{sessionTime(row.session)}</td> : null}
            <td>
              {row.session?.professional?.person?.name ?? 'Não informado'}
            </td>
            <td>{row.duration ?? '---'}</td>
            <td>
              <TableLabel tone={row.present ? 'success' : 'danger'}>
                {row.present ? 'Presente' : 'Ausente'}
              </TableLabel>
            </td>
            {!isAee ? (
              <td>
                <TableLabel tone={row.guardians ? 'success' : 'inverse'}>
                  {row.guardians ? 'Sim' : 'Não'}
                </TableLabel>
              </td>
            ) : null}
            <td>
              <AttendanceRowActions
                row={row}
                isAee={isAee}
                permissions={permissions}
              />
            </td>
          </tr>
        ))}
      </StudentRecordTable>
    </StudentSection>
  );
}

export function createStudentAttendanceTabs({
  student,
  permissions,
  aeeEvaluations,
  pedagogicalRecords,
}: StudentShowProps): TabItem[] {
  const canAee = permissions['aee-record.view-all']
    || permissions['aee-record.view-own'];
  const canPedagogical = permissions['pedagogical-record.view-all']
    || permissions['pedagogical-record.view-own'];
  const items: TabItem[] = [];

  if (canAee) {
    items.push({
      id: 'aee-attendance',
      label: 'Atendimentos AEE',
      icon: 'fa fa-calendar-check-o',
      content: (
        <AttendancePanel
          id="aee-records"
          title="Registros de Atendimentos AEE"
          iconClass="fa fa-calendar-check-o"
          emptyMessage="Nenhum registro de atendimento AEE encontrado."
          studentId={student.id}
          permissions={permissions}
          rows={aeeEvaluations.map((item) => ({
            id: item.id,
            session: item.aee_record?.attendance_session,
            duration: item.aee_record?.duration,
            present: item.is_present,
            guardians: undefined,
            canManage: item.can_manage ?? false,
            href: studentUrl(
              student.id,
              `aee-records/${item.id}/show`,
            ),
            editHref: studentUrl(
              student.id,
              `aee-records/${item.id}/edit`,
            ),
            deleteHref: studentUrl(student.id, `aee-records/${item.id}`),
          }))}
        />
      ),
    });
  }

  if (canPedagogical) {
    items.push({
      id: 'pedagogical-attendance',
      label: 'Atendimentos pedagógicos',
      icon: 'fa fa-comments-o',
      content: (
        <AttendancePanel
          id="pedagogical-records"
          title="Atendimentos Pedagógicos"
          iconClass="fa fa-comments-o"
          emptyMessage="Nenhum atendimento pedagógico encontrado."
          studentId={student.id}
          permissions={permissions}
          rows={pedagogicalRecords.map((item) => ({
            id: item.id,
            session: item.attendance_session,
            duration: item.duration,
            present: item.is_present,
            guardians: item.guardians.length > 0,
            canManage: item.can_manage ?? false,
            href: `${aeeBase}/pedagogical-records/${item.id}/show?student_id=${student.id}`,
            editHref: `${aeeBase}/pedagogical-records/${item.id}/edit?student_id=${student.id}`,
            deleteHref: `${aeeBase}/pedagogical-records/${item.id}?student_id=${student.id}`,
            pdfHref: `${aeeBase}/pedagogical-records/${item.id}/pdf`,
          }))}
        />
      ),
    });
  }

  return items;
}
