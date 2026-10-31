import { TableListCreateButton } from "@/components/Tables/TableList/TableList";
import Button from "@/components/UI/Button/Button";
import { aeeBase } from "@/components/Students/Students.utils";
import { sessionsBase } from "./SessionForm.utils";
import type { SessionMySessionsProps } from "./SessionList.types";
import SessionPage from "./SessionPage";
import SessionWorkspace from "./SessionWorkspace";

const mySessionsBase = `${aeeBase}/my-sessions`;

export default function InclusionSessionsMySessionsPage({
    sessions,
    calendar,
    view,
    filters,
    options,
    can,
}: SessionMySessionsProps): React.JSX.Element {
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
            subtitle="Acompanhe os seus atendimentos no calendário ou em formato de lista."
            breadcrumbItems={[
                { label: "Home", href: "/auth/dashboard" },
                { label: "Agendamentos", href: can.viewAll ? sessionsBase : undefined },
                { label: "Meus Agendamentos" },
            ]}
            actions={actions}
        >
            <SessionWorkspace
                baseUrl={mySessionsBase}
                sessions={sessions}
                calendar={calendar}
                filters={filters}
                options={options}
                view={view}
                canView={can.view}
                showProfessional={false}
                label="Meus agendamentos"
                failureText="Não foi possível atualizar seus agendamentos. Tente novamente."
            />
        </SessionPage>
    );
}
