import Panel from "@/Components/UI/Panel/Panel";
import Table from "@/Components/Tables/Table/Table";
import TableLabel from "@/Components/UI/TableLabel/TableLabel";
import Button from "@/Components/UI/Button/Button";
import { invoiceRows } from "./Invoice.data";
import "./Invoice.css";
export default function Invoice(): JSX.Element {
  return (
    <Panel
      className="invoice"
      body={
        <>
          <div className="invoice__top">
            <h4>
              <img src="/assets/gnai/images/logo_dark.png" alt="UBold" />
            </h4>
            <h4>
              Fatura #<br />
              <strong>2015-04-23654789</strong>
            </h4>
          </div>
          <hr />
          <div className="invoice__details">
            <address>
              <strong>Twitter, Inc.</strong>
              <br />
              795 Folsom Ave, Suite 600
              <br />
              San Francisco, CA 94107
              <br />
              <abbr title="Telefone">T:</abbr> (123) 456-7890
            </address>
            <div>
              <p>
                <strong>Data do pedido: </strong>15 jun. 2015
              </p>
              <p>
                <strong>Status do pedido: </strong>
                <TableLabel tone="pink">Pendente</TableLabel>
              </p>
              <p>
                <strong>ID do pedido: </strong>#123456
              </p>
            </div>
          </div>
          <div className="invoice__spacer" />
          <div className="invoice__table">
            <Table>
              <thead>
                <tr>
                  {[
                    "#",
                    "Item",
                    "Descrição",
                    "Quantidade",
                    "Valor unitário",
                    "Total",
                  ].map((label) => (
                    <th key={label}>{label}</th>
                  ))}
                </tr>
              </thead>
              <tbody>
                {invoiceRows.map((row, i) => (
                  <tr key={i}>
                    {row.map((text, j) => (
                      <td key={j}>{text}</td>
                    ))}
                  </tr>
                ))}
              </tbody>
            </Table>
          </div>
          <div className="invoice__totals">
            <p>
              <b>Subtotal:</b> 2930.00
            </p>
            <p>Desconto: 12.9%</p>
            <p>IVA: 12.9%</p>
            <hr />
            <h3>USD 2930.00</h3>
          </div>
          <hr />
          <div className="invoice__actions">
            <Button
              href="#"
              tone="inverse"
              aria-label="Imprimir"
              leftIconClass="fa fa-print"
            />{" "}
            <Button href="#" label="Enviar" />
          </div>
        </>
      }
    />
  );
}
