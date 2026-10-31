import Button from "@/components/UI/Button/Button";
import AeeRecordListPage from "./AeeRecordListPage";
import type {
    AeeListFilterName,
    AeeRecordListPageProps,
} from "./AeeRecordList.types";
import { aeeRecordsIndexUrl, myAeeRecordsUrl } from "./AeeRecord.utils";

const fields: AeeListFilterName[] = [
    "student",
    "professional_id",
    "course_id",
    "is_present",
];

export default function InclusionAeeRecordsIndexPage(
    props: AeeRecordListPageProps,
): React.JSX.Element {
    return (
        <AeeRecordListPage
            {...props}
            url={aeeRecordsIndexUrl}
            title="Todos os Atendimentos Especializados"
            subtitle="Visão geral dos registros de atendimento AEE dos profissionais permitidos."
            breadcrumbLabel="Atendimentos Especializados"
            idPrefix="aee-records"
            fields={fields}
            actions={props.can.view_own ? (
                <Button
                    href={myAeeRecordsUrl}
                    tone="list-action"
                    labelIconClass="fa fa-user"
                    aria-label="Ver meus atendimentos especializados"
                >
                    Meus Atendimentos
                </Button>
            ) : undefined}
        />
    );
}
