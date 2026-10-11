import Button from '@/components/UI/Button/Button';
import StudentSection, { StudentEmpty } from './StudentSection';
import type { StudentRecord, Permissions } from './Students.types';
import { aeeBase, studentUrl } from './Students.utils';

export default function StudentSupportRecord({ student, permissions }: { student: StudentRecord; permissions: Permissions }): React.JSX.Element {
  const context = student.current_context;
  return <>
    {permissions['student-deficiency.view'] ? <StudentSection id="deficiencias" title="Perfis de Atendimento do Aluno"
      actions={<Button href={studentUrl(student.id, 'deficiencies')} tone="warning">Gerenciar Perfis</Button>}>
      {student.deficiencies?.length ? <div className="studentsPage__grid">{student.deficiencies.map(item =>
        <div key={item.id} className="studentsPage__related"><strong>{item.name}</strong><p>GRAU: {item.severity_label ?? '—'}</p></div>)}</div>
        : <StudentEmpty>Nenhum perfil de atendimento do aluno encontrado.</StudentEmpty>}
    </StudentSection> : null}
    {permissions['guardian.view'] ? <StudentSection id="guardians" title="Responsáveis"
      actions={<Button href={studentUrl(student.id, 'guardians')} tone="warning">Gerenciar Responsáveis</Button>}>
      {student.guardians?.length ? <div className="studentsPage__grid">{student.guardians.map(guardian =>
        <div key={guardian.id} className="studentsPage__related">
          <div className="studentsPage__identity"><img className="studentsPage__avatar" src={guardian.person.photo_url} alt={`Foto de ${guardian.person.name}`} />
            <strong>{guardian.person.name}</strong></div>
          <p>{guardian.relationship_label}</p><p>Telefone: {guardian.person.phone || '---'}</p><p>E-mail: {guardian.person.email || '---'}</p>
        </div>)}</div> : <StudentEmpty>Nenhum responsável do aluno encontrado.</StudentEmpty>}
    </StudentSection> : null}
    {permissions['student-context.view'] ? <StudentSection id="contexts" title="Contexto Atual"
      actions={<Button href={`${aeeBase}/student-context/${student.id}/index`} tone="warning">Gerenciar Contextos</Button>}>
      {context ? <div className="studentsPage__grid">
        <div><strong>Contexto atual</strong><p>Semestre: {context.semester_label ?? '---'}</p>
          <p>Criado por: {context.evaluator?.person?.name ?? '---'}</p></div>
        <div><Button href={`${aeeBase}/student-context/${context.id}/show`} tone="info" aria-label="Visualizar contexto atual">Ver contexto</Button></div>
      </div> : <StudentEmpty>Nenhum contexto atual do aluno encontrado.</StudentEmpty>}
    </StudentSection> : null}
  </>;
}
