import { useState } from "react";
import Button from "@/components/UI/Button/Button";
import AssistiveTechnologyForm from "./AssistiveTechnologyForm";
import AssistiveTechnologyPage from "./AssistiveTechnologyPage";
import type {
    TechnologyFormOptions,
    TechnologyRecord,
} from "./AssistiveTechnologyForm.types";

export default function InclusiveRadarAssistiveTechnologiesCreatePage(
    props: TechnologyFormOptions & { cloneSource?: TechnologyRecord | null },
): React.JSX.Element {
    const [processing, setProcessing] = useState(false);
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
            actions={
                <Button
                    href="/inclusive-radar/assistive-technologies"
                    tone="cancel"
                    disabled={processing}
                >
                    Cancelar
                </Button>
            }
        >
            <AssistiveTechnologyForm
                mode="create"
                options={props}
                cloneSource={props.cloneSource}
                onProcessingChange={setProcessing}
            />
        </AssistiveTechnologyPage>
    );
}
