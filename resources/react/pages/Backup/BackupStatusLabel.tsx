import TableLabel from "@/components/UI/TableLabel/TableLabel";
import type { BackupRecord } from "./Backup.types";

function statusInfo(status: BackupRecord["status"]): { label: string; tone: string } {
    if (status === "success") return { label: "Sucesso", tone: "success" };
    if (status === "failed") return { label: "Falha", tone: "danger" };
    if (status === "archived") return { label: "Arquivado", tone: "info" };
    return { label: "Status desconhecido", tone: "inverse" };
}

export default function BackupStatusLabel({
    status,
}: {
    status: BackupRecord["status"];
}): React.JSX.Element {
    const display = statusInfo(status);
    return <TableLabel tone={display.tone}>{display.label}</TableLabel>;
}
