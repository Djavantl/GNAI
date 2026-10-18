import AssistiveTechnologyForm from "./AssistiveTechnologyForm";
import AssistiveTechnologyPage from "./AssistiveTechnologyPage";
import type {
    TechnologyFormOptions,
    TechnologyRecord,
} from "./AssistiveTechnologyForm.types";

export default function InclusiveRadarAssistiveTechnologiesEditPage(
    props: TechnologyFormOptions & {
        assistiveTechnology: TechnologyRecord;
        activeLoans: number;
    },
): React.JSX.Element {
    const technology = props.assistiveTechnology;
    return (
        <AssistiveTechnologyPage
            title="Editar Tecnologia Assistiva"
            current="Editar"
            technology={technology}
            subtitle={`Atualizando informações de: ${technology.name}`}
        >
            <AssistiveTechnologyForm
                mode="edit"
                options={props}
                technology={technology}
                activeLoans={props.activeLoans}
            />
        </AssistiveTechnologyPage>
    );
}
