import type { ReactNode } from 'react';
import FormSection from '@/components/Forms/FormSection/FormSection';
import type { TabItem } from '@/components/Navigation/Tabs/Tabs';
import TableActions from '@/components/Tables/TableActions/TableActions';
import {
  TableListCreateButton,
  type TableListColumn,
} from '@/components/Tables/TableList/TableList';
import Button from '@/components/UI/Button/Button';
import CardBox from '@/components/UI/CardBox/CardBox';
import TableLabel from '@/components/UI/TableLabel/TableLabel';
import DeleteStudentRecord from './DeleteStudentRecord';
import StudentRecordTable from './StudentRecordTable';
import StudentSection from './StudentSection';
import type { StudentOptions, StudentRecord } from './Students.types';
import { aeeBase, dateLabel } from './Students.utils';

const courseColumns: TableListColumn[] = [
  {
    id: 'course',
    label: 'Curso / Série',
    width: '38%',
    minWidth: 140,
    fixed: 'start',
  },
  { id: 'year', label: 'Ano letivo', width: '22%', minWidth: 90 },
  { id: 'status', label: 'Situação', width: '18%', minWidth: 90 },
  {
    id: 'actions',
    label: 'Ações',
    width: '22%',
    minWidth: 104,
    fixed: 'end',
  },
];

type StudentPersonalRecordProps = StudentOptions & {
  student: StudentRecord;
};

function Detail({
  label,
  children,
  wide = false,
}: {
  label: string;
  children: ReactNode;
  wide?: boolean;
}): React.JSX.Element {
  return (
    <div
      className={wide
        ? 'studentsPage__detail studentsPage__detail--wide'
        : 'studentsPage__detail'}
    >
      <dt>{label}:</dt>
      <dd>{children}</dd>
    </div>
  );
}

function PersonalDetails({
  student,
}: {
  student: StudentRecord;
}): React.JSX.Element {
  const person = student.person;

  return (
    <StudentSection
      id="dados-gerais"
      title="Dados Pessoais"
      iconClass="fa fa-user"
    >
      <dl className="studentsPage__detailGrid">
        <Detail label="Nome completo">{person.name}</Detail>
        <Detail label="Gênero">{person.gender_label || '---'}</Detail>
        <Detail label="CPF">{person.document || '---'}</Detail>
        <Detail label="Nascimento">{dateLabel(person.birth_date)}</Detail>
        <Detail label="Idade">
          {person.age != null ? `${person.age} anos` : '---'}
        </Detail>
        <Detail label="E-mail">{person.email || '---'}</Detail>
        <Detail label="Telefone">{person.phone || '---'}</Detail>
        <Detail label="Endereço" wide>
          {person.address_html ? (
            <div
              className="studentsPage__richText"
              dangerouslySetInnerHTML={{ __html: person.address_html }}
            />
          ) : '---'}
        </Detail>
      </dl>
    </StudentSection>
  );
}

function AcademicDetails({
  student,
  permissions,
  studentStatuses,
  statusColors,
}: StudentPersonalRecordProps): React.JSX.Element {
  return (
    <StudentSection
      id="informacoes-escolares"
      title="Informações Acadêmicas"
      iconClass="fa fa-graduation-cap"
    >
      <dl className="studentsPage__detailGrid">
        <Detail label="Status atual">
          <TableLabel tone={statusColors[student.status] ?? 'inverse'}>
            {studentStatuses[student.status] ?? student.status}
          </TableLabel>
        </Detail>
        <Detail label="Data de ingresso">
          {dateLabel(student.entry_date)}
        </Detail>
      </dl>
      <h3 className="studentsPage__subheading">Vínculo com cursos</h3>
      <StudentRecordTable
        ariaLabel="Cursos do aluno"
        columns={courseColumns}
        count={student.student_courses.length}
        emptyMessage="Nenhum curso do aluno encontrado."
        actions={permissions['student-course.create'] ? (
          <TableListCreateButton
            href={`${aeeBase}/student-courses/${student.id}/create`}
            ariaLabel="Cadastrar curso do aluno"
          />
        ) : undefined}
      >
        {student.student_courses.map((relation) => (
          <tr key={relation.id}>
            <td>
              <strong>
                {relation.course?.name ?? 'Curso não identificado'}
              </strong>
            </td>
            <td>{relation.academic_year}</td>
            <td>
              <TableLabel tone={relation.is_current ? 'success' : 'inverse'}>
                {relation.is_current ? 'Atual' : 'Histórico'}
              </TableLabel>
            </td>
            <td>
              {permissions['student-course.view']
              || permissions['student-course.delete'] ? (
                <TableActions>
                  {permissions['student-course.view'] ? (
                    <Button
                      href={`${aeeBase}/student-courses/${relation.id}`}
                      size="sm"
                      tone="dark-blue"
                      iconOnly
                      leftIconClass="fa fa-eye"
                      aria-label={`Visualizar curso ${relation.course?.name ?? ''}`}
                    />
                  ) : null}
                  {permissions['student-course.delete'] ? (
                    <DeleteStudentRecord
                      action={`${aeeBase}/student-courses/${relation.id}`}
                      title="Excluir matrícula"
                      message="Excluir esta matrícula do histórico do aluno?"
                      ariaLabel={`Excluir matrícula no curso ${relation.course?.name ?? ''}`}
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

export function createStudentPersonalTabs(
  props: StudentPersonalRecordProps,
): TabItem[] {
  return [
    {
      id: 'personal-data',
      label: 'Dados pessoais',
      icon: 'fa fa-user',
      content: <PersonalDetails student={props.student} />,
    },
    {
      id: 'academic-data',
      label: 'Informações acadêmicas',
      accessibleLabel: 'Informações acadêmicas do aluno',
      icon: 'fa fa-graduation-cap',
      content: <AcademicDetails {...props} />,
    },
  ];
}

export default function StudentPersonalRecord({
  student,
  permissions,
  studentStatuses,
  statusColors,
}: StudentPersonalRecordProps): React.JSX.Element {
  const person = student.person;
  const profileNames = permissions['student-deficiency.view']
    ? student.deficiencies?.map((item) => item.name).join(', ')
    : undefined;

  return (
    <CardBox className="studentsPage__summaryCard">
      <FormSection
        id="identificacao"
        title="Identificação do Aluno"
        iconClass="fa fa-user"
        variant="inline"
      >
        <div className="studentsPage__summary">
          <img
            src={person.photo_url}
            alt={`Foto de ${person.name}`}
            className="studentsPage__summaryPhoto"
          />
          <div className="studentsPage__summaryBody">
            <p className="studentsPage__summaryName">{person.name}</p>
            <dl className="studentsPage__summaryDetails">
              <Detail label="Matrícula">
                {student.registration || '---'}
              </Detail>
              <Detail label="Status">
                <TableLabel tone={statusColors[student.status] ?? 'inverse'}>
                  {studentStatuses[student.status] ?? student.status}
                </TableLabel>
              </Detail>
              {permissions['student-deficiency.view'] ? (
                <Detail label="Perfil de atendimento" wide>
                  {profileNames || 'Nenhum perfil cadastrado'}
                </Detail>
              ) : null}
            </dl>
          </div>
        </div>
      </FormSection>
    </CardBox>
  );
}
