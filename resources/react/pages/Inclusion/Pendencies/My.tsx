import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import Button from "@/components/UI/Button/Button";
import PendencyList, { pendenciesBase } from "./PendencyList";
import type { PendencyListProps } from "./PendencyList.types";

export default function InclusionPendenciesMyPage(
    props: PendencyListProps,
): React.JSX.Element {
    return (
        <BusinessPage
            title="Minhas Pendências"
            subtitle="Pendências atribuídas a você como responsável."
            breadcrumbItems={[
                { label: "Home", href: "/auth/dashboard" },
                { label: "Pendências", href: pendenciesBase },
                { label: "Minhas Pendências" },
            ]}
            actions={
                <Button
                    href={pendenciesBase}
                    tone="cancel"
                    leftIconClass="fa fa-arrow-left"
                    label="Voltar"
                    title="Voltar para pendências"
                    aria-label="Voltar para pendências"
                />
            }
            className="pendenciesPage"
        >
            <PendencyList
                {...props}
                variant="my"
            />
        </BusinessPage>
    );
}
