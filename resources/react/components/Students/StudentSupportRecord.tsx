import type { TabItem } from '@/components/Navigation/Tabs/Tabs';
import TableActions from '@/components/Tables/TableActions/TableActions';
import {
  TableListCreateButton,
  type TableListColumn,
} from '@/components/Tables/TableList/TableList';
import Button from '@/components/UI/Button/Button';
import TableLabel from '@/components/UI/TableLabel/TableLabel';
import DeleteStudentRecord from './DeleteStudentRecord';
import StudentRecordTable from './StudentRecordTable';
import StudentSection from './StudentSection';
import type { Permissions, StudentRecord } from './Students.types';
import { aeeBase, dateLabel } from './Students.utils';

interface StudentSupportRecordProps {
  student: StudentRecord;
  permissions: Permissions;
}

const profileColumns: TableListColumn[] = [
  {
    id: 'name',
    label: 'Perfil de atendimento',
    width: '46%',
    minWidth: 140,
    fixed: 'start',
  },
  { id: 'severity', label: 'Severidade', width: '26%', minWidth: 90 },
  {
    id: 'actions',
    label: 'Ações',
    width: '28%',
    minWidth: 104,
    fixed: 'end',
  },
];

const guardianColumns: TableListColumn[] = [
  {
    id: 'guardian',
    label: 'Responsável',
    width: '28%',
    minWidth: 140,
    fixed: 'start',
  },
  { id: 'document', label: 'CPF', width: '17%', minWidth: 100 },
  { id: 'relationship', label: 'Vínculo', width: '17%', minWidth: 90 },
  { id: 'contact', label: 'Contato', width: '22%', minWidth: 120 },
  {
    id: 'actions',
    label: 'Ações',
    width: '16%',
    minWidth: 104,
    fixed: 'end',
  },
];

const contextColumns: TableListColumn[] = [
  {
    id: 'date',
    label: 'Data',
    width: '18%',
    minWidth: 90,
    fixed: 'start',
  },
  { id: 'version', label: 'Versão', width: '14%', minWidth: 72 },
  {
    id: 'evaluation',
    label: 'Tipo de avaliação',
    width: '30%',
    minWidth: 120,
  },
  { id: 'status', label: 'Status', width: '16%', minWidth: 80 },
  {
    id: 'actions',
    label: 'Ações',
    width: '22%',
    minWidth: 104,
    fixed: 'end',
  },
];

function StudentProfiles({
  student,
  permissions,
}: StudentSupportRecordProps): React.JSX.Element {
  const profiles = student.deficiencies ?? [];

  return (
    <StudentSection
      id="deficiencias"
      title="Perfis de Atendimento do Aluno"
      iconClass="fa fa-wheelchair"
    >
      <StudentRecordTable
        ariaLabel="Perfis de atendimento do aluno"
        columns={profileColumns}
        count={profiles.length}
        emptyMessage="Nenhum perfil de atendimento do aluno encontrado."
        actions={permissions['student-deficiency.create'] ? (
          <TableListCreateButton
            href={`${aeeBase}/students/${student.id}/deficiencies/create`}
            ariaLabel="Cadastrar perfil de atendimento do aluno"
          />
        ) : undefined}
      >
        {profiles.map((profile) => (
          <tr key={profile.student_deficiency_id}>
            <td><strong>{profile.name}</strong></td>
            <td>
              {profile.severity_label ? (
                <TableLabel tone={profile.severity_color ?? 'inverse'}>
                  {profile.severity_label}
                </TableLabel>
              ) : 'Não informada'}
            </td>
            <td>
              {permissions['student-deficiency.view']
              || permissions['student-deficiency.delete'] ? (
                <TableActions>
                  {permissions['student-deficiency.view'] ? (
                    <Button
                      href={`${aeeBase}/students/${student.id}/deficiencies/${profile.student_deficiency_id}`}
                      size="sm"
                      tone="dark-blue"
                      iconOnly
                      leftIconClass="fa fa-eye"
                      aria-label={`Visualizar perfil ${profile.name}`}
                    />
                  ) : null}
                  {permissions['student-deficiency.delete'] ? (
                    <DeleteStudentRecord
                      action={`${aeeBase}/students/${student.id}/deficiencies/${profile.student_deficiency_id}`}
                      title="Excluir perfil de atendimento"
                      message="Remover este perfil de atendimento do registro do aluno?"
                      ariaLabel={`Excluir perfil ${profile.name}`}
                    />
                  ) : null}
                </TableActions>
              ) : 'Nenhuma ação'}
            </td>
          </tr>
        ))}
      </StudentRecordTable>
    </StudentSection>
  );
}

function StudentGuardians({
  student,
  permissions,
}: StudentSupportRecordProps): React.JSX.Element {
  const guardians = student.guardians ?? [];

  return (
    <StudentSection
      id="guardians"
      title="Responsáveis"
      iconClass="fa fa-users"
    >
      <StudentRecordTable
        ariaLabel="Responsáveis do aluno"
        columns={guardianColumns}
        count={guardians.length}
        emptyMessage="Nenhum responsável encontrado para este aluno."
        actions={permissions['guardian.create'] ? (
          <TableListCreateButton
            href={`${aeeBase}/students/${student.id}/guardians/create`}
            ariaLabel="Cadastrar responsável do aluno"
          />
        ) : undefined}
      >
        {guardians.map((guardian) => (
          <tr key={guardian.id}>
            <td>
              <div className="studentsPage__identity">
                <img
                  className="studentsPage__avatar"
                  src={guardian.person.photo_url}
                  alt={`Foto de ${guardian.person.name}`}
                />
                <strong>{guardian.person.name}</strong>
              </div>
            </td>
            <td>{guardian.person.document || 'Não informado'}</td>
            <td>{guardian.relationship_label}</td>
            <td>
              {guardian.person.email
                || guardian.person.phone
                || 'Não informado'}
            </td>
            <td>
              {permissions['guardian.view'] || permissions['guardian.delete'] ? (
                <TableActions>
                  {permissions['guardian.view'] ? (
                    <Button
                      href={`${aeeBase}/guardian/${guardian.id}/show`}
                      size="sm"
                      tone="dark-blue"
                      iconOnly
                      leftIconClass="fa fa-eye"
                      aria-label={`Visualizar responsável ${guardian.person.name}`}
                    />
                  ) : null}
                  {permissions['guardian.delete'] ? (
                    <DeleteStudentRecord
                      action={`${aeeBase}/students/${student.id}/guardians/${guardian.id}`}
                      title="Excluir responsável"
                      message={(
                        <>
                          Remover o vínculo de <strong>{guardian.person.name}</strong>?
                        </>
                      )}
                      ariaLabel={`Excluir responsável ${guardian.person.name}`}
                    />
                  ) : null}
                </TableActions>
              ) : 'Nenhuma ação'}
            </td>
          </tr>
        ))}
      </StudentRecordTable>
    </StudentSection>
  );
}

function StudentContexts({
  student,
  permissions,
}: StudentSupportRecordProps): React.JSX.Element {
  const contexts = student.contexts ?? [];
  const createHref = contexts.length
    ? `${aeeBase}/student-context/${student.id}/new-version`
    : `${aeeBase}/student-context/${student.id}/create`;

  return (
    <StudentSection
      id="contexts"
      title="Histórico de Contextos"
      iconClass="fa fa-file-text-o"
    >
      <StudentRecordTable
        ariaLabel="Histórico de contextos do aluno"
        columns={contextColumns}
        count={contexts.length}
        emptyMessage="Nenhum contexto do aluno encontrado."
        actions={permissions['student-context.create'] ? (
          <TableListCreateButton
            href={createHref}
            ariaLabel={contexts.length
              ? 'Cadastrar nova versão do contexto'
              : 'Cadastrar contexto do aluno'}
          />
        ) : undefined}
      >
        {contexts.map((context) => (
          <tr key={context.id}>
            <td><strong>{dateLabel(context.created_at)}</strong></td>
            <td>V{context.version}</td>
            <td>{context.evaluation_type_label}</td>
            <td>
              <TableLabel tone={context.is_current ? 'success' : 'inverse'}>
                {context.is_current ? 'Atual' : 'Histórico'}
              </TableLabel>
            </td>
            <td>
              {permissions['student-context.view']
              || (permissions['student-context.delete'] && context.can_manage) ? (
                <TableActions>
                  {permissions['student-context.view'] ? (
                    <Button
                      href={`${aeeBase}/student-context/${context.id}/show`}
                      size="sm"
                      tone="dark-blue"
                      iconOnly
                      leftIconClass="fa fa-eye"
                      aria-label={`Visualizar contexto versão ${context.version}`}
                    />
                  ) : null}
                  {permissions['student-context.delete'] && context.can_manage ? (
                    <DeleteStudentRecord
                      action={`${aeeBase}/student-context/${context.id}`}
                      title="Excluir contexto do aluno"
                      message={`Excluir permanentemente o contexto V${context.version}?`}
                      ariaLabel={`Excluir contexto versão ${context.version}`}
                    />
                  ) : null}
                </TableActions>
              ) : 'Nenhuma ação'}
            </td>
          </tr>
        ))}
      </StudentRecordTable>
    </StudentSection>
  );
}

export function createStudentSupportTabs({
  student,
  permissions,
}: StudentSupportRecordProps): TabItem[] {
  const items: TabItem[] = [];

  if (permissions['student-deficiency.view']) {
    items.push({
      id: 'support-profiles',
      label: 'Perfis de atendimento',
      icon: 'fa fa-wheelchair',
      content: (
        <StudentProfiles student={student} permissions={permissions} />
      ),
    });
  }

  if (permissions['guardian.view']) {
    items.push({
      id: 'guardians',
      label: 'Responsáveis',
      icon: 'fa fa-users',
      content: (
        <StudentGuardians student={student} permissions={permissions} />
      ),
    });
  }

  if (permissions['student-context.view']) {
    items.push({
      id: 'current-context',
      label: 'Contextos',
      icon: 'fa fa-file-text-o',
      content: (
        <StudentContexts student={student} permissions={permissions} />
      ),
    });
  }

  return items;
}
