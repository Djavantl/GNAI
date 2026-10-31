import FormSection from "@/components/Forms/FormSection/FormSection";
import DeleteStudentRecord from "@/components/Students/DeleteStudentRecord";
import StudentRecordTable from "@/components/Students/StudentRecordTable";
import { aeeBase, dateLabel } from "@/components/Students/Students.utils";
import TableActions from "@/components/Tables/TableActions/TableActions";
import {
    TableListCreateButton,
    type TableListColumn,
} from "@/components/Tables/TableList/TableList";
import Button from "@/components/UI/Button/Button";
import type { PeiDisciplineSummary } from "./PeiShow.types";

const columns: TableListColumn[] = [
    {
        id: "discipline",
        label: "Disciplina",
        width: "32%",
        minWidth: 140,
        fixed: "start",
    },
    { id: "teacher", label: "Professor responsável", width: "28%", minWidth: 130 },
    { id: "created", label: "Cadastrada em", width: "16%", minWidth: 96 },
    {
        id: "actions",
        label: "Ações",
        width: "24%",
        minWidth: 150,
        fixed: "end",
    },
];

export default function PeiDisciplineTable({
    peiId,
    disciplines,
    canView,
    canCreate,
    canPdf,
}: {
    peiId: number;
    disciplines: PeiDisciplineSummary[] | null;
    canView: boolean;
    canCreate: boolean;
    canPdf: boolean;
}): React.JSX.Element {
    const disciplinesUrl = `${aeeBase}/peis/${peiId}/disciplines`;
    const createButton = canCreate ? (
        <TableListCreateButton
            href={`${disciplinesUrl}/create`}
            ariaLabel="Cadastrar adaptação curricular"
        />
    ) : undefined;
    const items = disciplines ?? [];

    return (
        <FormSection
            id="pei-disciplines"
            title="Adaptações Razoáveis e/ou Acessibilidades Curriculares"
            iconClass="fa fa-book"
            variant="inline"
        >
            {canView ? (
                <StudentRecordTable
                    ariaLabel="Adaptações curriculares do PEI"
                    columns={columns}
                    count={items.length}
                    emptyMessage="Nenhuma adaptação razoável e/ou acessibilidade curricular cadastrada."
                    actions={createButton}
                >
                    {items.map((item) => {
                        const name = item.discipline_name || "disciplina não identificada";

                        return (
                            <tr key={item.id}>
                                <td>
                                    <strong>{item.discipline_name || "Não informado"}</strong>
                                </td>
                                <td>{item.teacher_name || "Não informado"}</td>
                                <td>{dateLabel(item.created_at)}</td>
                                <td>
                                    <TableActions>
                                        <Button
                                            href={`${disciplinesUrl}/${item.id}/show`}
                                            size="sm"
                                            tone="dark-blue"
                                            iconOnly
                                            leftIconClass="fa fa-eye"
                                            aria-label={`Visualizar adaptação de ${name}`}
                                        />
                                        {canPdf ? (
                                            <Button
                                                href={`${aeeBase}/peis/${peiId}/disciplinas/${item.id}/pdf`}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                size="sm"
                                                tone="dark-red"
                                                iconOnly
                                                leftIconClass="fa fa-file-pdf-o"
                                                aria-label={`Gerar PDF da adaptação de ${name}`}
                                            />
                                        ) : null}
                                        {item.can_update ? (
                                            <Button
                                                href={`${disciplinesUrl}/${item.id}/edit`}
                                                size="sm"
                                                tone="warning"
                                                iconOnly
                                                leftIconClass="fa fa-pencil"
                                                aria-label={`Editar adaptação de ${name}`}
                                            />
                                        ) : null}
                                        {item.can_delete ? (
                                            <DeleteStudentRecord
                                                action={`${disciplinesUrl}/${item.id}`}
                                                title="Excluir Adaptação"
                                                message="Deseja realmente excluir esta adaptação?"
                                                ariaLabel={`Excluir adaptação de ${name}`}
                                            />
                                        ) : null}
                                    </TableActions>
                                </td>
                            </tr>
                        );
                    })}
                </StudentRecordTable>
            ) : (
                createButton ? (
                    <div className="studentsPage__recordListActions">
                        {createButton}
                    </div>
                ) : null
            )}
        </FormSection>
    );
}
