import GuardianForm from "./GuardianForm";
import GuardianPage from "./GuardianPage";
import type { GuardianFormOptions, GuardianRecord } from "./Guardian.types";

export default function InclusionGuardiansEditPage(
    props: GuardianFormOptions & { guardian: GuardianRecord },
): React.JSX.Element {
    return (
        <GuardianPage
            title="Editar Responsável"
            subtitle={`Editando vínculo de: ${props.guardian.person.name}`}
            current="Editar"
            student={props.student}
            guardian={props.guardian}
        >
            <GuardianForm
                key={props.guardian.id}
                mode="edit"
                student={props.student}
                genders={props.genders}
                relationships={props.relationships}
                guardian={props.guardian}
            />
        </GuardianPage>
    );
}
