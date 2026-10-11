import CardBox from "@/components/UI/CardBox/CardBox";
import CrmToolbar from "@/components/UI/CrmToolbar/CrmToolbar";
import PeopleTable from "@/components/Tables/PeopleTable/PeopleTable";
import { sellers } from "./EcommerceSellers.data";
export default function EcommerceSellers(): React.JSX.Element {
  return (
    <CardBox>
      <CrmToolbar
        kind="person"
        addLabel="Adicionar vendedor"
        searchLabel="Pesquisar vendedores"
      />
      <PeopleTable records={sellers} variant="sellers" />
    </CardBox>
  );
}
