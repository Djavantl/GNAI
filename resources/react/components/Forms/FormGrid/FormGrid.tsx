import type { ReactNode } from "react";
import { joinClassNames } from "@/utils/joinClassNames";
import "./FormGrid.css";

export default function FormGrid({
    children,
    columns = 2,
    className,
}: {
    children: ReactNode;
    columns?: 2 | 3 | 4;
    className?: string;
}): React.JSX.Element {
    return (
        <div
            className={joinClassNames(
                "formGrid",
                `formGrid--${columns}`,
                className,
            )}
        >
            {children}
        </div>
    );
}

export function FormGridWide({
    children,
    className,
}: {
    children: ReactNode;
    className?: string;
}): React.JSX.Element {
    return (
        <div className={joinClassNames("formGrid__wide", className)}>
            {children}
        </div>
    );
}

export function FormStack({
    children,
    className,
}: {
    children: ReactNode;
    className?: string;
}): React.JSX.Element {
    return (
        <div className={joinClassNames("formStack", className)}>{children}</div>
    );
}
