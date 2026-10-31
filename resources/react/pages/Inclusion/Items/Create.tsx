import ItemForm from "./ItemForm";
import ItemPage from "./ItemPage";
import type { ItemFormOptions, ItemRecord } from "./ItemForm.types";

export default function InclusionItemsCreatePage(
    props: ItemFormOptions & { cloneSource?: ItemRecord | null },
): React.JSX.Element {
    const cloning = !!props.cloneSource;

    return (
        <ItemPage
            title={cloning ? "Clonar Item" : "Novo Item"}
            current="Cadastrar"
            subtitle="Cadastre tecnologias assistivas ou materiais pedagógicos acessíveis e realize a vistoria inicial."
        >
            <ItemForm
                mode="create"
                options={props}
                cloneSource={props.cloneSource}
            />
        </ItemPage>
    );
}
