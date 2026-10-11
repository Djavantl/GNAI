import CardBox from "@/components/UI/CardBox/CardBox";
import ContactSearch from "@/components/UI/ContactSearch/ContactSearch";
import SortButtonGroup from "@/components/UI/SortButtonGroup/SortButtonGroup";
import ProductOrdersTable from "@/components/Tables/ProductOrdersTable/ProductOrdersTable";
import { orders, sortOptions } from "./EcommerceOrders.data";
import "./EcommerceOrders.css";
export default function EcommerceOrders(): React.JSX.Element {
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
