import FormSection from "@/components/Forms/FormSection/FormSection";
import {
    TableListCard,
    TableListCreateButton,
} from "@/components/Tables/TableList/TableList";
import Button from "@/components/UI/Button/Button";
import { aeeBase } from "@/components/Students/Students.utils";
import SessionFilters from "./SessionFilters";
import { sessionsBase } from "./SessionForm.utils";
import type {
    SessionIndexProps,
    SessionTableFilterKey,
} from "./SessionList.types";
import SessionPage from "./SessionPage";
import SessionTable from "./SessionTable";
import SessionWeeklyAgenda from "./SessionWeeklyAgenda";
import { sessionQuery, useSessionListRequest } from "./useSessionListRequest";

const tableFilterFields: SessionTableFilterKey[] = ["student", "professional", "type", "status"];

export default function SpecializedEducationalSupportSessionsIndexPage({
    sessions,
    agenda,
    weekNavigation,
    filters,
    options,
    can,
}: SessionIndexProps): React.JSX.Element {
    const request = useSessionListRequest("Não foi possível atualizar os agendamentos. Tente novamente.");
    const actions = can.create || can.mySessions ? (
        <>
            {can.mySessions ? (
                <Button
                    href={`${aeeBase}/my-sessions`}
                    tone="inverse"
                    leftIconClass="fa fa-clock-o"
                    label="Meus agendamentos"
                    title="Meus agendamentos"
                />
            ) : null}
            {can.create ? (
                <TableListCreateButton
                    href={`${sessionsBase}/create`}
                    ariaLabel="Cadastrar agendamento"
                />
            ) : null}
        </>
    ) : undefined;

    return (
        <SessionPage
            title="Agendamentos"
            subtitle="Veja os agendamentos cadastrados em tabela e acompanhe a agenda semanal."
            actions={actions}
        >
            <SessionWeeklyAgenda
                title="Agenda semanal"
                subtitle="Visão da equipe inteira, organizada por dia da semana."
                agenda={agenda}
                weekNavigation={weekNavigation}
                filters={filters}
                students={options.students ?? []}
                professionals={options.professionals ?? []}
                canView={can.view}
                loading={request.loading}
                onSearch={(values) => request.visit(
                    sessionsBase,
                    sessionQuery({ ...filters, ...values }, sessions.current_page),
                )}
                onNavigate={(url) => request.visit(url)}
            />
            <TableListCard
                aria-label="Listagem de agendamentos"
                loading={request.loading}
                loadingLabel="Atualizando agendamentos"
            >
                <FormSection
                    id="session-list"
                    variant="inline"
                    title="Lista de agendamentos"
                    iconClass="fa fa-list"
                >
                    <SessionFilters
                        fields={tableFilterFields}
                        filters={filters}
                        options={options}
                        disabled={request.loading}
                        onSearch={(values) => request.visit(
                            sessionsBase,
                            sessionQuery({ ...filters, ...values }),
                        )}
                    />
                    <SessionTable
                        sessions={sessions}
                        canView={can.view}
                        showStudents
                        showProfessional
                        loading={request.loading}
                        onPage={(page) => request.visit(
                            sessionsBase,
                            sessionQuery({ ...filters }, page),
                        )}
                    />
                </FormSection>
            </TableListCard>
        </SessionPage>
    );
}
