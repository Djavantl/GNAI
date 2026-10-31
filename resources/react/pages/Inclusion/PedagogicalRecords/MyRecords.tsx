import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import { aeeBase } from "@/components/Students/Students.utils";
import Button from "@/components/UI/Button/Button";
import PedagogicalRecordList from "./PedagogicalRecordList";
import type { PedagogicalRecordListProps } from "./PedagogicalRecordList.types";

export default function InclusionPedagogicalRecordsMyRecordsPage(
    props: PedagogicalRecordListProps,
): React.JSX.Element {
    return (
        <BusinessPage
            title="Meus Atendimentos Pedagógicos"
            subtitle="Histórico dos seus atendimentos pedagógicos."
            breadcrumbItems={[
                { label: "Home", href: "/auth/dashboard" },
                { label: "Atendimentos Pedagógicos" },
            ]}
            actions={props.can.view_all ? (
                <Button
                    href={`${aeeBase}/pedagogical-records`}
                    tone="cancel"
                    leftIconClass="fa fa-users"
                >
                    Todos os Atendimentos
                </Button>
            ) : undefined}
        >
            <PedagogicalRecordList
                {...props}
                url={`${aeeBase}/my-pedagogical-records`}
                variant="general"
                primaryFilters={["student", "course_id", "is_present", "with_guardians"]}
            />
        </BusinessPage>
    );
}
