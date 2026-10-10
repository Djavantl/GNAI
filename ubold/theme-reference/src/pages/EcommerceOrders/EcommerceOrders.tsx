import CardBox from "@/Components/UI/CardBox/CardBox";
import ContactSearch from "@/Components/UI/ContactSearch/ContactSearch";
import SortButtonGroup from "@/Components/UI/SortButtonGroup/SortButtonGroup";
import ProductOrdersTable from "@/Components/Tables/ProductOrdersTable/ProductOrdersTable";
import { orders, sortOptions } from "./EcommerceOrders.data";
import "./EcommerceOrders.css";
export default function EcommerceOrders(): JSX.Element {
  return (
    <CardBox>
      <div className="ecommerceOrders__toolbar">
        <div className="ecommerceOrders__search">
          <ContactSearch label="Pesquisar pedidos" />
        </div>
        <div className="ecommerceOrders__sort">
          <SortButtonGroup
            type="radio"
            options={sortOptions}
            initialSelected={["Status"]}
          />
        </div>
      </div>
      <ProductOrdersTable records={orders} />
    </CardBox>
  );
}
