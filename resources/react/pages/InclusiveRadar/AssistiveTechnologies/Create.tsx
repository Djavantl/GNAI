import AssistiveTechnologyForm from "./AssistiveTechnologyForm";
import AssistiveTechnologyPage from "./AssistiveTechnologyPage";
import type {
    TechnologyFormOptions,
    TechnologyRecord,
} from "./AssistiveTechnologyForm.types";

export default function InclusiveRadarAssistiveTechnologiesCreatePage(
    props: TechnologyFormOptions & { cloneSource?: TechnologyRecord | null },
): React.JSX.Element {
    const cloning = !!props.cloneSource;
    return (
        <AssistiveTechnologyPage
            title={
                cloning
                    ? "Clonar Tecnologia Assistiva"
                    : "Nova Tecnologia Assistiva"
            }
            current="Cadastrar"
            subtitle="Cadastre novos recursos institucionais e realize a vistoria inicial."
        >
            <AssistiveTechnologyForm
                mode="create"
                options={props}
                cloneSource={props.cloneSource}
            />
        </AssistiveTechnologyPage>
    );
}
