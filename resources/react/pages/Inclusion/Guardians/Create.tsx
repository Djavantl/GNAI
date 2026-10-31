import GuardianForm from "./GuardianForm";
import GuardianPage from "./GuardianPage";
import type { GuardianFormOptions } from "./Guardian.types";

export default function InclusionGuardiansCreatePage(
    props: GuardianFormOptions & { defaultGender?: string },
): React.JSX.Element {
    return (
        <GuardianPage
            title="Cadastrar Responsável"
            subtitle={`Aluno: ${props.student.person.name}`}
            current="Cadastrar"
            student={props.student}
        >
            <GuardianForm
                mode="create"
                student={props.student}
                genders={props.genders}
                relationships={props.relationships}
                defaultGender={props.defaultGender}
            />
        </GuardianPage>
    );
}
