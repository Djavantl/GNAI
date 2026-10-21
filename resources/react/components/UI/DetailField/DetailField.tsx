import type { ReactNode } from "react";
import "./DetailField.css";

export default function DetailField({
    label,
    children,
    wide = false,
}: {
    label: string;
    children: ReactNode;
    wide?: boolean;
}): React.JSX.Element {
    return (
        <div className={`detailField${wide ? " detailField--wide" : ""}`}>
            <dt>{label}:</dt>
            <dd>{children}</dd>
        </div>
    );
}
