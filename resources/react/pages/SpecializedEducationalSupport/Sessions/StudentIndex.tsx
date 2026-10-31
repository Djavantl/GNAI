import {
    TableListCard,
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
import { sessionQuery, useSessionListRequest } from "./useSessionListRequest";

const tableFilterFields: SessionTableFilterKey[] = ["professional", "type", "status"];

export default function SpecializedEducationalSupportSessionsStudentIndexPage({
    sessions,
    student,
    filters,
    options,
    can,
}: SessionStudentIndexProps): React.JSX.Element {
    const request = useSessionListRequest("Não foi possível atualizar os agendamentos do aluno. Tente novamente.");
    const listUrl = studentUrl(student.id, "sessions");
    const currentFilters = {
        professional: filters.professional,
        type: filters.type,
        status: filters.status,
    };

    return (
        <SessionPage
            title={`Agendamentos de Atendimento — ${student.name}`}
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
                loading={request.loading}
                loadingLabel="Atualizando agendamentos do aluno"
            >
                <SessionFilters
                    fields={tableFilterFields}
                    filters={filters}
                    options={options}
                    disabled={request.loading}
                    onSearch={(values) => request.visit(listUrl, sessionQuery(values))}
                />
                <SessionTable
                    sessions={sessions}
                    canView={can.view}
                    showProfessional
                    showTime
                    loading={request.loading}
                    emptyMessage="Nenhum agendamento encontrado para este aluno."
                    onPage={(page) => request.visit(listUrl, sessionQuery(currentFilters, page))}
                />
            </TableListCard>
        </SessionPage>
    );
}
