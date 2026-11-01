import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import TableActions from "@/components/Tables/TableActions/TableActions";
import {
    TableListCard,
    TableListEmpty,
    TableListFooter,
    TableListScroll,
    TableListTable,
    useTableListAction,
    useTableListVisit,
    type TableListColumn,
} from "@/components/Tables/TableList/TableList";
import Button from "@/components/UI/Button/Button";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import "./NotificationsIndex.css";

type NotificationRecord = {
    id: string;
    title: string;
    /** Ícone da seção da sidebar à qual a notificação leva. */
    icon_class: string;
    message: string;
    url: string | null;
    is_unread: boolean;
    created_at: string | null;
    created_at_human: string | null;
};

type NotificationPaginator = {
    data: NotificationRecord[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
};

type Props = {
    notifications: NotificationPaginator;
};

const base = "/notifications";
const columns: TableListColumn[] = [
    { id: "title", label: "Notificação", width: "26%", minWidth: 140, fixed: "start" },
    { id: "message", label: "Mensagem", width: "38%", minWidth: 160 },
    { id: "date", label: "Recebida", width: "14%", minWidth: 100 },
    { id: "status", label: "Status", width: "11%", minWidth: 80 },
    { id: "actions", label: "Ações", width: "11%", minWidth: 104, fixed: "end" },
];

export default function NotificationsIndexPage({ notifications }: Props): React.JSX.Element {
    const list = useTableListVisit({
        url: base,
        failureText: "Não foi possível atualizar as notificações. Tente novamente.",
    });
    const readAll = useTableListAction<never>({
        failureText: "Não foi possível marcar as notificações como lidas. Tente novamente.",
    });
    const readOne = useTableListAction<never>({
        failureText: "Não foi possível marcar a notificação como lida. Tente novamente.",
    });
    const actionBusy = readAll.processing || readOne.processing;

    function markAllAsRead(): void {
        if (actionBusy) return;
        readAll.submit("post", `${base}/read-all`);
    }

    function markAsRead(notification: NotificationRecord): void {
        if (actionBusy) return;
        readOne.submit("post", `${base}/${encodeURIComponent(notification.id)}/read`);
    }

    return (
        <BusinessPage
            title="Notificações"
            subtitle="Histórico completo das suas notificações do sistema."
            breadcrumbItems={[
                { label: "Home", href: "/auth/dashboard" },
                { label: "Notificações" },
            ]}
            actions={(
                <Button
                    type="button"
                    tone="list-action"
                    labelIconClass="fa fa-check"
                    disabled={actionBusy}
                    onClick={markAllAsRead}
                >
                    Marcar todas como lidas
                </Button>
            )}
            className="notificationIndex"
        >
            <TableListCard
                aria-label="Listagem de notificações"
                aria-busy={list.loading}
                loading={list.loading}
                loadingLabel="Atualizando notificações"
            >
                <TableListScroll aria-label="Tabela de notificações">
                    <TableListTable
                        className="notificationIndex__table"
                        columns={columns}
                        page={notifications}
                    >
                        {notifications.data.length ? notifications.data.map((notification) => (
                            <tr
                                key={notification.id}
                                className={notification.is_unread ? "notificationIndex__row--unread" : undefined}
                            >
                                <td title={notification.title}>
                                    <span className="notificationIndex__title">
                                        <i
                                            className={`${notification.icon_class} notificationIndex__icon`}
                                            aria-hidden="true"
                                        />
                                        <span className="notificationIndex__titleText">
                                            {notification.title}
                                        </span>
                                    </span>
                                </td>
                                <td title={notification.message || undefined}>
                                    {notification.message || "Não informado"}
                                </td>
                                <td>
                                    {notification.created_at ? (
                                        <time dateTime={notification.created_at}>
                                            <i className="fa fa-clock-o notificationIndex__clock" aria-hidden="true" />
                                            {notification.created_at_human}
                                        </time>
                                    ) : "Não informado"}
                                </td>
                                <td>
                                    <TableLabel tone={notification.is_unread ? "purple" : "inverse"}>
                                        {notification.is_unread ? "Não lida" : "Lida"}
                                    </TableLabel>
                                </td>
                                <td>
                                    <TableActions>
                                        {notification.url ? (
                                            <Button
                                                href={notification.url}
                                                size="sm"
                                                tone="save"
                                                iconOnly
                                                leftIconClass="fa fa-arrow-right"
                                                title="Abrir"
                                                aria-label={`Abrir ${notification.title}`}
                                            />
                                        ) : null}
                                        {notification.is_unread ? (
                                            <Button
                                                type="button"
                                                size="sm"
                                                tone="success"
                                                iconOnly
                                                leftIconClass="fa fa-check"
                                                title="Marcar como lida"
                                                aria-label={`Marcar como lida: ${notification.title}`}
                                                disabled={actionBusy}
                                                onClick={() => markAsRead(notification)}
                                            />
                                        ) : null}
                                        {!notification.url && !notification.is_unread ? <span>Sem ações</span> : null}
                                    </TableActions>
                                </td>
                            </tr>
                        )) : (
                            <tr>
                                <TableListEmpty colSpan={5}>
                                    <i className="fa fa-bell-o notificationIndex__emptyIcon" aria-hidden="true" />
                                    Nenhuma notificação encontrada
                                </TableListEmpty>
                            </tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={notifications}
                    disabled={list.loading}
                    paginationLabel="Paginação de notificações"
                    onSelect={(page) => list.visit({}, page)}
                />
            </TableListCard>
        </BusinessPage>
    );
}
