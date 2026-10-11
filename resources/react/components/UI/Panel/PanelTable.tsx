import Table from '@/components/Tables/Table/Table';
import './Panel.css';

interface PanelTableProps {
  headers: string[];
  rows: string[][];
}

export default function PanelTable({ headers, rows }: PanelTableProps): React.JSX.Element {
  return (
    <Table appearance="inherit" className="panelCard__table table">
      <thead>
        <tr>
          {headers.map((header: string) => (
            <th key={header}>{header}</th>
          ))}
        </tr>
      </thead>
      <tbody>
        {rows.map((row: string[]) => (
          <tr key={row.join('-')}>
            {row.map((cell: string, index: number) =>
              index === 0 ? (
                <th key={`${row.join('-')}-${cell}`} scope="row">
                  {cell}
                </th>
              ) : (
                <td key={`${row.join('-')}-${cell}`}>{cell}</td>
              )
            )}
          </tr>
        ))}
      </tbody>
    </Table>
  );
}
