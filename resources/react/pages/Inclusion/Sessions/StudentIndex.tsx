import {
    TableListCard,
    useTableListVisit,
} from "@/components/Tables/TableList/TableList";
import Button from "@/components/UI/Button/Button";
import {
    studentsBase,
    studentUrl,
} from "@/components/Students/Students.utils";
import SessionFilters from "./SessionFilters";
import type {
    SessionStudentIndexProps,
    SessionTableFilterKey,
} from "./SessionList.types";
import SessionPage from "./SessionPage";
import SessionTable from "./SessionTable";

const tableFilterFields: SessionTableFilterKey[] = ["professional", "type", "status"];

export default function InclusionSessionsStudentIndexPage({
    sessions,
    student,
    filters,
    options,
    can,
}: SessionStudentIndexProps): React.JSX.Element {
    const list = useTableListVisit({
        url: studentUrl(student.id, "sessions"),
        failureText: "Não foi possível atualizar os agendamentos do aluno. Tente novamente.",
    });
    const currentFilters = {
        professional: filters.professional,
        type: filters.type,
        status: filters.status,
    };

    return (
        <SessionPage
            title={`Agendamentos de Atendimento, ${student.name}`}
            subtitle="Histórico de atendimentos realizados para o aluno."
            breadcrumbItems={[
                { label: "Home", href: "/auth/dashboard" },
                { label: "Alunos", href: studentsBase },
                { label: student.name, href: studentUrl(student.id) },
                { label: "Agendamentos" },
            ]}
            actions={(
                <Button
                    href={studentUrl(student.id)}
                    tone="cancel"
                    leftIconClass="fa fa-arrow-left"
                    label="Voltar"
                    title="Voltar ao prontuário"
                    aria-label="Voltar ao prontuário do aluno"
                />
            )}
        >
            <TableListCard
                aria-label={`Listagem de agendamentos de ${student.name}`}
                loading={list.loading}
                loadingLabel="Atualizando agendamentos do aluno"
            >
                <SessionFilters
                    fields={tableFilterFields}
                    filters={filters}
                    options={options}
                    onSearch={(values) => list.visit(values)}
                />
                <SessionTable
                    sessions={sessions}
                    canView={can.view}
                    showProfessional
                    showTime
                    loading={list.loading}
                    emptyMessage="Nenhum agendamento encontrado para este aluno."
                    onPage={(page) => list.visit(currentFilters, page)}
                />
            </TableListCard>
        </SessionPage>
    );
}
