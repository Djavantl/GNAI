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
    SessionMySessionsProps,
    SessionTableFilterKey,
} from "./SessionList.types";
import SessionPage from "./SessionPage";
import SessionTable from "./SessionTable";
import SessionWeeklyAgenda from "./SessionWeeklyAgenda";
import { sessionQuery, useSessionListRequest } from "./useSessionListRequest";

const mySessionsBase = `${aeeBase}/my-sessions`;
const tableFilterFields: SessionTableFilterKey[] = ["student", "type", "status"];

export default function SpecializedEducationalSupportSessionsMySessionsPage({
    sessions,
    agenda,
    weekNavigation,
    filters,
    options,
    can,
}: SessionMySessionsProps): React.JSX.Element {
    const request = useSessionListRequest("Não foi possível atualizar seus agendamentos. Tente novamente.");
    const actions = can.create || can.viewAll ? (
        <>
            {can.viewAll ? (
                <Button
                    href={sessionsBase}
                    tone="inverse"
                    leftIconClass="fa fa-calendar"
                    label="Todos Agendamentos"
                    title="Voltar para agendamentos"
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
            title="Meus Agendamentos"
            subtitle="Veja seus agendamentos cadastrados em tabela e acompanhe a agenda semanal."
            breadcrumbItems={[
                { label: "Home", href: "/auth/dashboard" },
                { label: "Agendamentos", href: can.viewAll ? sessionsBase : undefined },
                { label: "Meus Agendamentos" },
            ]}
            actions={actions}
        >
            <SessionWeeklyAgenda
                title="Minha agenda semanal"
                subtitle="Visão dos seus agendamentos, organizada por dia da semana."
                agenda={agenda}
                weekNavigation={weekNavigation}
                filters={filters}
                students={options.students ?? []}
                canView={can.view}
                loading={request.loading}
                onSearch={(values) => request.visit(
                    mySessionsBase,
                    sessionQuery({ ...filters, ...values }, sessions.current_page),
                )}
                onNavigate={(url) => request.visit(url)}
            />
            <TableListCard
                aria-label="Listagem dos meus agendamentos"
                loading={request.loading}
                loadingLabel="Atualizando seus agendamentos"
            >
                <FormSection
                    id="my-session-list"
                    variant="inline"
                    title="Lista dos meus agendamentos"
                    iconClass="fa fa-list"
                >
                    <SessionFilters
                        fields={tableFilterFields}
                        filters={filters}
                        options={options}
                        disabled={request.loading}
                        onSearch={(values) => request.visit(
                            mySessionsBase,
                            sessionQuery({ ...filters, ...values }),
                        )}
                    />
                    <SessionTable
                        sessions={sessions}
                        canView={can.view}
                        showStudents
                        loading={request.loading}
                        onPage={(page) => request.visit(
                            mySessionsBase,
                            sessionQuery({ ...filters }, page),
                        )}
                    />
                </FormSection>
            </TableListCard>
        </SessionPage>
    );
}
