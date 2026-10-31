import Button from "@/components/UI/Button/Button";
import AeeRecordListPage from "./AeeRecordListPage";
import type {
    AeeListFilterName,
    AeeRecordListPageProps,
} from "./AeeRecordList.types";
import { aeeRecordsIndexUrl, myAeeRecordsUrl } from "./AeeRecord.utils";

const fields: AeeListFilterName[] = [
    "student",
    "course_id",
    "is_present",
];

export default function InclusionAeeRecordsMyRecordsPage(
    props: AeeRecordListPageProps,
): React.JSX.Element {
    return (
        <AeeRecordListPage
            {...props}
            url={myAeeRecordsUrl}
            title="Meus Atendimentos Especializados"
            subtitle="Histórico de registros dos seus atendimentos AEE."
            breadcrumbLabel="Atendimentos AEE"
            idPrefix="my-aee-records"
            fields={fields}
            actions={props.can.view_all ? (
                <Button
                    href={aeeRecordsIndexUrl}
                    tone="list-action"
                    labelIconClass="fa fa-users"
                    aria-label="Ver todos os atendimentos especializados"
                >
                    Todos os Atendimentos
                </Button>
            ) : undefined}
        />
    );
}
