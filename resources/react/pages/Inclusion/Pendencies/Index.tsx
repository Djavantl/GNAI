import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import { TableListCreateButton } from "@/components/Tables/TableList/TableList";
import Button from "@/components/UI/Button/Button";
import PendencyList, {
    myPendenciesUrl,
    pendenciesBase,
} from "./PendencyList";
import type { PendencyListProps } from "./PendencyList.types";

export default function InclusionPendenciesIndexPage(
    props: PendencyListProps,
): React.JSX.Element {
    const { can } = props;
    const actions = can.my || can.create ? (
        <>
            {can.my ? (
                <Button
                    href={myPendenciesUrl}
                    tone="info"
                    leftIconClass="fa fa-user"
                    label="Minhas Pendências"
                />
            ) : null}
            {can.create ? (
                <TableListCreateButton
                    href={`${pendenciesBase}/create`}
                    ariaLabel="Cadastrar pendência"
                />
            ) : null}
        </>
    ) : undefined;

    return (
        <BusinessPage
            title="Pendências"
            subtitle="Gerencie as pendências e seus responsáveis."
            breadcrumbItems={[
                { label: "Home", href: "/auth/dashboard" },
                { label: "Pendências" },
            ]}
            actions={actions}
            className="pendenciesPage"
        >
            <PendencyList
                {...props}
                variant="all"
            />
        </BusinessPage>
    );
}
