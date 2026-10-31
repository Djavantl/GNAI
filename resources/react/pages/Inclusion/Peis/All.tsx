import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import PeiList from "./PeiList";
import type { PeiListProps } from "./PeiList.types";

export default function InclusionPeisAllPage(
    props: PeiListProps,
): React.JSX.Element {
    return (
        <BusinessPage
            title="Listagem Geral de PEIs"
            subtitle="Visualização consolidada de todos os planos e adaptações curriculares do campus."
            breadcrumbItems={[
                { label: "Home", href: "/auth/dashboard" },
                { label: "PEIs" },
            ]}
            className="peisPage"
        >
            <PeiList
                {...props}
                variant="all"
            />
        </BusinessPage>
    );
}
