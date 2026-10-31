import { TableListCreateButton } from "@/components/Tables/TableList/TableList";
import Button from "@/components/UI/Button/Button";
import { aeeBase } from "@/components/Students/Students.utils";
import { sessionsBase } from "./SessionForm.utils";
import type { SessionIndexProps } from "./SessionList.types";
import SessionPage from "./SessionPage";
import SessionWorkspace from "./SessionWorkspace";

export default function InclusionSessionsIndexPage({
    sessions,
    calendar,
    filters,
    options,
    can,
    view,
}: SessionIndexProps): React.JSX.Element {
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
            subtitle="Acompanhe os atendimentos do NAPNE no calendário ou em formato de lista."
            actions={actions}
        >
            <SessionWorkspace
                baseUrl={sessionsBase}
                sessions={sessions}
                calendar={calendar}
                filters={filters}
                options={options}
                view={view}
                canView={can.view}
                label="Central de agendamentos"
                failureText="Não foi possível atualizar os agendamentos. Tente novamente."
            />
        </SessionPage>
    );
}
