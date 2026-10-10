import CardBox from "@/Components/UI/CardBox/CardBox";
import CrmToolbar from "@/Components/UI/CrmToolbar/CrmToolbar";
import PeopleTable from "@/Components/Tables/PeopleTable/PeopleTable";
import { sellers } from "./EcommerceSellers.data";
export default function EcommerceSellers(): JSX.Element {
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
