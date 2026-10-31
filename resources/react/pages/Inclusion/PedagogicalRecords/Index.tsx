import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import { aeeBase } from "@/components/Students/Students.utils";
import Button from "@/components/UI/Button/Button";
import PedagogicalRecordList from "./PedagogicalRecordList";
import type { PedagogicalRecordListProps } from "./PedagogicalRecordList.types";

export default function InclusionPedagogicalRecordsIndexPage(
    props: PedagogicalRecordListProps,
): React.JSX.Element {
    return (
        <BusinessPage
            title="Todos os Atendimentos Pedagógicos"
            subtitle="Visão geral dos registros pedagógicos dos profissionais permitidos."
            breadcrumbItems={[
                { label: "Home", href: "/auth/dashboard" },
                { label: "Atendimentos Pedagógicos" },
            ]}
            actions={props.can.view_own ? (
                <Button
                    href={`${aeeBase}/my-pedagogical-records`}
                    tone="cancel"
                    leftIconClass="fa fa-user"
                >
                    Meus Atendimentos
                </Button>
            ) : undefined}
        >
            <PedagogicalRecordList
                {...props}
                url={`${aeeBase}/pedagogical-records`}
                variant="general"
                primaryFilters={["student", "professional_id", "course_id"]}
                moreFilters={["is_present", "with_guardians"]}
            />
        </BusinessPage>
    );
}
