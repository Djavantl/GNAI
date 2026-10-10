import Table from "@/Components/Tables/Table/Table";
import ImageDisplay from "@/Components/UI/ImageDisplay/ImageDisplay";
import RecordActions from "@/Components/UI/RecordActions/RecordActions";
import TableLabel from "@/Components/UI/TableLabel/TableLabel";
import type {
  ProductOrderRecord,
  ProductOrdersVariant,
} from "./ProductOrdersTable.types";
import "./ProductOrdersTable.css";
export default function ProductOrdersTable({
  records,
  variant = "orders",
}: {
  records: ProductOrderRecord[];
  variant?: ProductOrdersVariant;
}): JSX.Element {
  return (
    <div className="productOrdersTable__scroll">
      <Table className="productOrdersTable">
        <thead>
          <tr>
            <th>Produto</th>
            <th>
              {variant === "popular" ? "Data de inclusão" : "Data do pedido"}
            </th>
            <th>{variant === "popular" ? "Pedidos" : "Número do pedido"}</th>
            {variant === "orders" && (
              <>
                <th>Vendedor</th>
                <th>Status</th>
              </>
            )}
            <th>Valor</th>
            <th className="productOrdersTable__action">Ação</th>
          </tr>
        </thead>
        <tbody>
          {records.map((record) => (
            <tr key={record.id}>
              <td>
                <ImageDisplay
                  src={record.image}
                  alt={
                    record.image.split("/").pop()?.replace(".jpg", "") ??
                    "Produto"
                  }
                  size="sm"
                />{" "}
              </td>
              <td>{record.date}</td>
              <td>
                {variant === "popular" ? (
                  <b>{record.number}</b>
                ) : (
                  <a href="#">{record.number}</a>
                )}
              </td>
              {variant === "orders" && (
                <>
                  <td>
                    <a href="#" className="productOrdersTable__seller">
                      <b>{record.seller}</b>
                    </a>
                  </td>
                  <td>
                    <TableLabel tone={record.tone}>{record.status}</TableLabel>
                  </td>
                </>
              )}
              <td>{record.amount}</td>
              <td>
                <RecordActions name={`pedido ${record.number}`} />
              </td>
            </tr>
          ))}
        </tbody>
      </Table>
    </div>
  );
}
