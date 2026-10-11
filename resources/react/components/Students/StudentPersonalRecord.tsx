import Badge from '@/components/UI/Badge/Badge';
import Button from '@/components/UI/Button/Button';
import StudentSection, { StudentEmpty } from './StudentSection';
import type { StudentRecord, StudentOptions } from './Students.types';
import { dateLabel, studentUrl } from './Students.utils';

export default function StudentPersonalRecord({ student, permissions, studentStatuses, statusColors }: StudentOptions & { student: StudentRecord }): React.JSX.Element {
  const person = student.person;
  const status = <Badge variant={statusColors[student.status] ?? 'default'}>{studentStatuses[student.status] ?? student.status}</Badge>;
  return <>
    <StudentSection id="identificacao" title="Identificação do Aluno">
      <div className="studentsPage__profile">
        <img src={person.photo_url} alt={`Foto de ${person.name}`} className="studentsPage__photo" />
        <div className="studentsPage__status">{status}</div><h3>{person.name}</h3><p>Matrícula: {student.registration}</p>
      </div>
    </StudentSection>
    <StudentSection id="dados-gerais" title="Dados Pessoais">
      <dl className="studentsPage__grid studentsPage__data">
        {[
          ['Nome Completo', person.name], ['Gênero', person.gender_label], ['CPF', person.document],
          ['Nascimento', dateLabel(person.birth_date)], ['Idade', person.age != null ? `${person.age} anos` : null],
          ['E-mail', person.email], ['Telefone', person.phone],
        ].map(([label, value]) => <div key={label}><dt>{label}</dt><dd>{value || '---'}</dd></div>)}
        <div className="studentsPage__wide"><dt>Endereço</dt><dd>{person.address_html
          ? <div dangerouslySetInnerHTML={{ __html: person.address_html }} /> : '---'}</dd></div>
      </dl>
    </StudentSection>
    <StudentSection id="informacoes-escolares" title="Informações Acadêmicas"
      actions={permissions['student-course.view'] ? <Button href={studentUrl(student.id, 'history')} tone="warning">Gerenciar Cursos</Button> : undefined}>
      <dl className="studentsPage__grid studentsPage__data">
        <div><dt>Status Atual</dt><dd>{status}</dd></div><div><dt>Data de Ingresso</dt><dd>{dateLabel(student.entry_date)}</dd></div>
      </dl><h3>Vínculo com Cursos</h3>
      {student.student_courses.length ? student.student_courses.map(relation => <div key={relation.id} className="studentsPage__related">
        <strong>{relation.course?.name ?? 'Curso não identificado'}</strong><p>Ano Acadêmico: {relation.academic_year}</p>
        {relation.is_current ? <Badge variant="success">ATUAL</Badge> : null}
      </div>) : <StudentEmpty>Nenhum curso ativo do aluno encontrado.</StudentEmpty>}
    </StudentSection>
  </>;
}
