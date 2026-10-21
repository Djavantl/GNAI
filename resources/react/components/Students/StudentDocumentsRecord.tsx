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
import {
  aeeBase,
  dateLabel,
  dateTimeLabel,
  fileSizeLabel,
} from './Students.utils';

interface StudentDocumentsRecordProps {
  student: StudentRecord;
  permissions: Permissions;
}

const peiColumns: TableListColumn[] = [
  {
    id: 'semester',
    label: 'Semestre',
    width: '21%',
    minWidth: 100,
    fixed: 'start',
  },
  { id: 'course', label: 'Curso', width: '22%', minWidth: 110 },
  { id: 'status', label: 'Status', width: '15%', minWidth: 80 },
  { id: 'current', label: 'Atual', width: '12%', minWidth: 64 },
  { id: 'version', label: 'Versão', width: '10%', minWidth: 64 },
  {
    id: 'actions',
    label: 'Ações',
    width: '20%',
    minWidth: 104,
    fixed: 'end',
  },
];

const documentColumns: TableListColumn[] = [
  {
    id: 'title',
    label: 'Título',
    width: '25%',
    minWidth: 130,
    fixed: 'start',
  },
  { id: 'type', label: 'Tipo', width: '13%', minWidth: 82 },
  { id: 'semester', label: 'Semestre', width: '13%', minWidth: 82 },
  { id: 'size', label: 'Tamanho', width: '11%', minWidth: 76 },
  { id: 'upload', label: 'Upload', width: '16%', minWidth: 110 },
  {
    id: 'actions',
    label: 'Ações',
    width: '22%',
    minWidth: 150,
    fixed: 'end',
  },
];

function StudentPeis({
  student,
  permissions,
}: StudentDocumentsRecordProps): React.JSX.Element {
  const peis = student.peis ?? [];

  return (
    <StudentSection
      id="peis"
      title="PEIs (Plano Educacional Individualizado)"
      iconClass="fa fa-map-o"
    >
      <StudentRecordTable
        ariaLabel="Planos Educacionais Individualizados do aluno"
        columns={peiColumns}
        count={peis.length}
        emptyMessage="Nenhum PEI do aluno encontrado."
        actions={permissions['pei.create'] ? (
          <TableListCreateButton
            href={`${aeeBase}/students/${student.id}/peis/create`}
            ariaLabel="Cadastrar PEI do aluno"
          />
        ) : undefined}
      >
        {peis.map((pei) => (
          <tr key={pei.id}>
            <td>
              <strong>{pei.semester_label ?? 'Não informado'}</strong>
              <small className="studentsPage__tableSecondary">
                Criado em {dateLabel(pei.created_at)}
              </small>
            </td>
            <td>{pei.course?.name ?? 'Não informado'}</td>
            <td>
              <TableLabel tone={pei.is_finished ? 'success' : 'warning'}>
                {pei.is_finished ? 'Finalizado' : 'Em aberto'}
              </TableLabel>
            </td>
            <td>{pei.is_current ? 'Sim' : 'Não'}</td>
            <td>V{pei.version}</td>
            <td>
              {permissions['pei.view'] || permissions['pei.delete'] ? (
                <TableActions>
                  {permissions['pei.view'] ? (
                    <Button
                      href={`${aeeBase}/peis/${pei.id}/show`}
                      size="sm"
                      tone="dark-blue"
                      iconOnly
                      leftIconClass="fa fa-eye"
                      aria-label={`Visualizar PEI versão ${pei.version}`}
                    />
                  ) : null}
                  {permissions['pei.delete'] ? (
                    <DeleteStudentRecord
                      action={`${aeeBase}/peis/${pei.id}`}
                      title="Excluir PEI"
                      message={`Excluir o PEI versão ${pei.version}?`}
                      ariaLabel={`Excluir PEI versão ${pei.version}`}
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

function StudentDocuments({
  student,
  permissions,
}: StudentDocumentsRecordProps): React.JSX.Element {
  const documents = student.documents ?? [];

  return (
    <StudentSection
      id="documents"
      title="Documentos do Aluno"
      iconClass="fa fa-folder-open-o"
    >
      <StudentRecordTable
        ariaLabel="Documentos do aluno"
        columns={documentColumns}
        count={documents.length}
        emptyMessage="Nenhum documento do aluno encontrado."
        actions={permissions['student-document.create'] ? (
          <TableListCreateButton
            href={`${aeeBase}/students/${student.id}/documents/create`}
            ariaLabel="Cadastrar documento do aluno"
          />
        ) : undefined}
      >
        {documents.map((document) => (
          <tr key={document.id}>
            <td>
              <strong>{document.title}</strong>
              <small className="studentsPage__tableSecondary">
                {document.original_name}
              </small>
            </td>
            <td>{document.type_label}</td>
            <td>{document.semester_label ?? 'Não informado'}</td>
            <td>{fileSizeLabel(document.file_size)}</td>
            <td>{dateTimeLabel(document.created_at)}</td>
            <td>
              {permissions['student-document.view']
              || permissions['student-document.update']
              || permissions['student-document.delete'] ? (
                <TableActions>
                  {permissions['student-document.view'] ? (
                    <>
                      <Button
                        href={`${aeeBase}/student-documents/${document.id}/view`}
                        target="_blank"
                        rel="noopener noreferrer"
                        size="sm"
                        tone="dark-blue"
                        iconOnly
                        leftIconClass="fa fa-eye"
                        aria-label={`Visualizar documento ${document.title}`}
                      />
                      <Button
                        href={`${aeeBase}/student-documents/${document.id}/download`}
                        target="_blank"
                        rel="noopener noreferrer"
                        size="sm"
                        tone="cancel"
                        iconOnly
                        leftIconClass="fa fa-download"
                        aria-label={`Baixar documento ${document.title}`}
                      />
                    </>
                  ) : null}
                  {permissions['student-document.update'] ? (
                    <Button
                      href={`${aeeBase}/student-documents/${document.id}/edit`}
                      size="sm"
                      tone="warning"
                      iconOnly
                      leftIconClass="fa fa-pencil"
                      aria-label={`Editar documento ${document.title}`}
                    />
                  ) : null}
                  {permissions['student-document.delete'] ? (
                    <DeleteStudentRecord
                      action={`${aeeBase}/student-documents/${document.id}`}
                      title="Excluir documento"
                      message={(
                        <>
                          Excluir permanentemente o documento{' '}
                          <strong>{document.title}</strong>?
                        </>
                      )}
                      ariaLabel={`Excluir documento ${document.title}`}
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

export function createStudentDocumentTabs({
  student,
  permissions,
}: StudentDocumentsRecordProps): TabItem[] {
  const items: TabItem[] = [];

  if (permissions['pei.view']) {
    items.push({
      id: 'peis',
      label: 'PEIs',
      accessibleLabel: 'Planos Educacionais Individualizados',
      icon: 'fa fa-map-o',
      content: <StudentPeis student={student} permissions={permissions} />,
    });
  }

  if (permissions['student-document.view']) {
    items.push({
      id: 'documents',
      label: 'Documentos',
      icon: 'fa fa-folder-open-o',
      content: (
        <StudentDocuments student={student} permissions={permissions} />
      ),
    });
  }

  return items;
}
