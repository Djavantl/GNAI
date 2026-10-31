import Button from '@/components/UI/Button/Button';
import { TableListEmpty, TableListScroll, TableListTable } from '@/components/Tables/TableList/TableList';
import { reportCells } from './Reports.utils';
import type { ReportResult } from './Reports.types';

export default function ReportPreview({ result, busy, onCopy }: {
    result: ReportResult | null;
    busy: boolean;
    onCopy: () => void;
}): React.JSX.Element {
    if (!result) {
        return <p className="reportBuilder__empty">A prévia do relatório aparecerá aqui. Escolha as informações e clique em Gerar prévia.</p>;
    }
    const headers = Object.entries(result.headers);
    const rows = reportCells(result);
    return (
        <div>
            <TableListScroll aria-label="Prévia do relatório">
                <TableListTable
                    key={headers.map(([key]) => key).join('|')}
                    columns={headers.map(([id, label]) => ({ id, label, hideable: false }))}
                    summary={`${result.total} registro${result.total === 1 ? '' : 's'}`}
                    className="reportBuilder__preview"
                >
                    {rows.map((row, index) => (
                        <tr key={index}>
                            {row.map((cell, column) => <td key={headers[column][0]} title={cell}>{cell}</td>)}
                        </tr>
                    ))}
                    {!rows.length ? <TableListEmpty colSpan={Math.max(1, headers.length)}>Nenhum resultado encontrado para os filtros aplicados.</TableListEmpty> : null}
                </TableListTable>
            </TableListScroll>
            <div className="reportBuilder__previewFooter">
                <span>{result.total} linha(s) · {headers.length} coluna(s)</span>
                <Button label="Copiar" tone="cancel" leftIconClass="fa fa-copy" disabled={busy || !rows.length} onClick={onCopy} />
            </div>
        </div>
    );
}
