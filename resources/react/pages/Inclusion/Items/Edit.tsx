import ItemForm from "./ItemForm";
import ItemPage from "./ItemPage";
import type { ItemFormOptions, ItemRecord } from "./ItemForm.types";

export default function InclusionItemsEditPage(
    props: ItemFormOptions & {
        item: ItemRecord;
        activeLoans: number;
    },
): React.JSX.Element {
    return (
        <ItemPage
            title="Editar Item"
            current="Editar"
            item={props.item}
            subtitle={`Atualizando informações de: ${props.item.name}`}
        >
            <ItemForm
                key={props.item.id}
                mode="edit"
                options={props}
                item={props.item}
                activeLoans={props.activeLoans}
            />
        </ItemPage>
    );
}
