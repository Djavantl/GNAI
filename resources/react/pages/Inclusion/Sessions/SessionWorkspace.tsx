import PageTabs from "@/components/Navigation/PageTabs/PageTabs";
import {
    TableListCard,
    useTableListVisit,
} from "@/components/Tables/TableList/TableList";
import SessionCalendar from "./SessionCalendar";
import SessionIndexFilters, {
    sessionIndexFilterValues,
} from "./SessionIndexFilters";
import type {
    SessionCalendarData,
    SessionListFilters,
    SessionListOptions,
    SessionListPaginator,
    SessionView,
} from "./SessionList.types";
import SessionTable from "./SessionTable";

const views = [
    {
        key: "calendar",
        label: "Calendário",
        iconClass: "fa fa-calendar",
    },
    {
        key: "list",
        label: "Listagem",
        iconClass: "fa fa-list",
    },
] as const;

function viewUrl(
    baseUrl: string,
    view: SessionView,
    filters: ReturnType<typeof sessionIndexFilterValues>,
): string {
    const query = new URLSearchParams({ view });
    Object.entries(filters).forEach(([key, value]) => {
        if (value) query.set(key, value);
    });
    return `${baseUrl}?${query.toString()}`;
}

/** Abas, filtros compartilhados e visualização em calendário ou listagem dos agendamentos. */
export default function SessionWorkspace({
    baseUrl,
    sessions,
    calendar,
    filters,
    options,
    view,
    canView,
    showProfessional = true,
    label,
    failureText,
}: {
    baseUrl: string;
    sessions: SessionListPaginator;
    calendar: SessionCalendarData;
    filters: SessionListFilters;
    options: SessionListOptions;
    view: SessionView;
    canView: boolean;
    showProfessional?: boolean;
    label: string;
    failureText: string;
}): React.JSX.Element {
    const list = useTableListVisit({ url: baseUrl, failureText });
    const activeFilters = sessionIndexFilterValues(filters);

    return (
        <>
            <PageTabs
                items={views.map((item) => ({
                    ...item,
                    href: viewUrl(baseUrl, item.key, activeFilters),
                }))}
                activeKey={view}
                ariaLabel={`Visualizações de ${label.toLowerCase()}`}
            />
            <TableListCard
                className="sessionsPage__workspace"
                aria-label={label}
                loading={list.loading}
                loadingLabel={`Atualizando ${label.toLowerCase()}`}
            >
                <SessionIndexFilters
                    filters={filters}
                    options={options}
                    showProfessional={showProfessional}
                    onSearch={(values) => list.visit(values, 1, { view })}
                />

                {view === "calendar" ? (
                    <SessionCalendar
                        calendar={calendar}
                        referenceDate={filters.week}
                        canView={canView}
                        onReferenceDate={(date) =>
                            list.visit(
                                { ...activeFilters, week: date },
                                1,
                                { view: "calendar" },
                            )
                        }
                    />
                ) : (
                    <div className="sessionsPage__listView">
                        <SessionTable
                            sessions={sessions}
                            canView={canView}
                            showStudents
                            showProfessional={showProfessional}
                            showTime
                            loading={list.loading}
                            onPage={(page) => list.visit(activeFilters, page, { view: "list" })}
                        />
                    </div>
                )}
            </TableListCard>
        </>
    );
}
