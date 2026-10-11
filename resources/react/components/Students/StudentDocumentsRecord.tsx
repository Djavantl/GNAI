import Table from '@/components/Tables/Table/Table';
import Button from '@/components/UI/Button/Button';
import Badge from '@/components/UI/Badge/Badge';
import StudentSection from './StudentSection';
import type { StudentRecord, Permissions } from './Students.types';
import { aeeBase, dateLabel, studentUrl } from './Students.utils';

export default function StudentDocumentsRecord({ student, permissions }: { student: StudentRecord; permissions: Permissions }): React.JSX.Element {
  return <>
    {permissions['pei.view'] ? <StudentSection id="peis" title="PEIs (Plano Educacional Individualizado)"
      actions={<><small>São exibidos até 5 registros mais recentes. Para consultar todos, clique em “Gerenciar PEIs”.</small>
        <Button href={studentUrl(student.id, 'peis')} tone="warning">Gerenciar PEIs</Button></>}>
      <div className="studentsPage__table" role="region" aria-label="PEIs" tabIndex={0}><Table hover>
        <thead><tr>{['Semestre', 'Curso', 'Professor', 'Status', 'Ações'].map(label => <th scope="col" key={label}>{label}</th>)}</tr></thead>
        <tbody>{student.peis?.length ? student.peis.map(pei => <tr key={pei.id}>
          <td>{pei.semester_label ?? 'Não informado'}</td><td>{pei.course?.name ?? 'Geral'}</td>
          <td>{pei.teacher_names.join(', ') || 'Não informado'}</td>
          <td><Badge variant={pei.is_finished ? 'success' : 'warning'}>{pei.is_finished ? 'Finalizado' : 'Em andamento'}</Badge></td>
          <td><Button href={`${aeeBase}/peis/${pei.id}/show`} tone="info" aria-label={`Visualizar PEI do semestre ${pei.semester_label ?? ''}`}>Ver</Button></td>
        </tr>) : <tr><td colSpan={5} className="studentsPage__empty">Nenhum PEI encontrado.</td></tr>}</tbody>
      </Table></div>
    </StudentSection> : null}
    {permissions['student-document.view'] ? <StudentSection id="documents" title="Documentos do Aluno"
      actions={<><small>São exibidos até 5 registros mais recentes. Para consultar todos, clique em “Gerenciar Documentos”.</small>
        <Button href={studentUrl(student.id, 'documents')} tone="warning">Gerenciar Documentos</Button></>}>
      <div className="studentsPage__table" role="region" aria-label="Documentos" tabIndex={0}><Table hover>
        <thead><tr>{['Título', 'Tipo', 'Upload', 'Ações'].map(label => <th scope="col" key={label}>{label}</th>)}</tr></thead>
        <tbody>{student.documents?.length ? student.documents.map(document => <tr key={document.id}>
          <td><strong>{document.title}</strong><br /><small>{document.original_name}</small></td>
          <td>{document.type_label}</td><td>{dateLabel(document.created_at)}</td>
          <td><Button href={`${aeeBase}/student-documents/${document.id}/view`} target="_blank" rel="noopener noreferrer" tone="info"
            aria-label={`Visualizar documento ${document.title} em nova aba`}>Ver documento</Button></td>
        </tr>) : <tr><td colSpan={4} className="studentsPage__empty">Nenhum documento encontrado.</td></tr>}</tbody>
      </Table></div>
    </StudentSection> : null}
  </>;
}
