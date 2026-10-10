import { useId, useState } from "react";
import Table from "@/Components/Tables/Table/Table";
import ChoiceControl from "@/Components/Forms/ChoiceControl/ChoiceControl";
import ImageDisplay from "@/Components/UI/ImageDisplay/ImageDisplay";
import DropdownMenu from "@/Components/Navigation/DropdownMenu/DropdownMenu";
import RecordActions from "@/Components/UI/RecordActions/RecordActions";
import type { PersonRecord } from "./PeopleTable.types";
import { peopleActions } from "./PeopleTable.data";
import "./PeopleTable.css";
export default function PeopleTable({
  records,
  variant = "contacts",
}: {
  records: PersonRecord[];
  variant?: "contacts" | "sellers";
}): JSX.Element {
  const id = useId();
  const [selected, setSelected] = useState<Set<string>>(
    () =>
      new Set(
        records.filter((record) => record.checked).map((record) => record.id),
      ),
  );
  const [headerChecked, setHeaderChecked] = useState(false);
  return (
    <div className="peopleTable__scroll">
      <Table
        hover
        className={`peopleTable${variant === "sellers" ? " peopleTable--sellers" : ""}`}
      >
        <thead>
          <tr>
            <th>
              <ChoiceControl
                type="checkbox"
                id={`${id}-all`}
                tone="primary"
                single
                aria-label="Seleção do cabeçalho"
                checked={headerChecked}
                onChange={setHeaderChecked}
              />{" "}
              <DropdownMenu
                label={null}
                accessibleLabel="Ações dos registros"
                tone="white"
                size="xs"
                wavesLight
                items={peopleActions}
              />
            </th>
            <th>Nome</th>
            <th>E-mail</th>
            {variant === "sellers" && <th>Produtos</th>}
            <th>Data de início</th>
            <th>Ação</th>
          </tr>
        </thead>
        <tbody>
          {records.map((record) => (
            <tr
              key={record.id}
              className={record.active ? "active" : undefined}
            >
              <td>
                <ChoiceControl
                  type="checkbox"
                  id={`${id}-${record.id}`}
                  tone="primary"
                  checked={selected.has(record.id)}
                  aria-label={`Selecionar ${record.name}`}
                  onChange={(checked) =>
                    setSelected((current) => {
                      const next = new Set(current);
                      checked ? next.add(record.id) : next.delete(record.id);
                      return next;
                    })
                  }
                />{" "}
                <ImageDisplay
                  src={record.avatar}
                  alt={`Foto de ${record.name}`}
                  shape="circle"
                  size="sm"
                />
              </td>
              <td>{record.name}</td>
              <td>
                <a href="#">{record.email}</a>
              </td>
              {variant === "sellers" && (
                <td>
                  <b>
                    <a href="">
                      <b>{record.productCount}</b>
                    </a>
                  </b>
                </td>
              )}
              <td>{record.date}</td>
              <td>
                <RecordActions name={record.name} />
              </td>
            </tr>
          ))}
        </tbody>
      </Table>
    </div>
  );
}
