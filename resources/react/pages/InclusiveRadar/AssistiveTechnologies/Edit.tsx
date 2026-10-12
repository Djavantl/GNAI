import { useState } from "react";
import Button from "@/components/UI/Button/Button";
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
    const [processing, setProcessing] = useState(false);
    const technology = props.assistiveTechnology;
    return (
        <AssistiveTechnologyPage
            title="Editar Tecnologia Assistiva"
            current="Editar"
            technology={technology}
            subtitle={`Atualizando informações de: ${technology.name}`}
            actions={
                <Button
                    href={`/inclusive-radar/assistive-technologies/${technology.id}`}
                    tone="cancel"
                    disabled={processing}
                >
                    Cancelar
                </Button>
            }
        >
            <AssistiveTechnologyForm
                mode="edit"
                options={props}
                technology={technology}
                activeLoans={props.activeLoans}
                onProcessingChange={setProcessing}
            />
        </AssistiveTechnologyPage>
    );
}
