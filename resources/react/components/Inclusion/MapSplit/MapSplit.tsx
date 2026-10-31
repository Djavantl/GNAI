import type { ReactNode } from "react";
import "./MapSplit.css";

/**
 * Divide o conteúdo de um card com mapa: dados em `MapSplitMain`, à esquerda,
 * e mapa com seus controles em `MapSplitAside`, uma coluna menor à direita.
 * Em telas estreitas as colunas se empilham na ordem do código.
 */
export default function MapSplit({
    children,
    wideAside = false,
}: {
    children: ReactNode;
    /** Coluna do mapa mais larga, para telas em que o mapa fica sozinho à direita. */
    wideAside?: boolean;
}): React.JSX.Element {
    return <div className={wideAside ? "mapSplit mapSplit--wideAside" : "mapSplit"}>{children}</div>;
}

export function MapSplitMain({ children }: { children: ReactNode }): React.JSX.Element {
    return <div className="mapSplit__main">{children}</div>;
}

export function MapSplitAside({ children }: { children: ReactNode }): React.JSX.Element {
    return <div className="mapSplit__aside">{children}</div>;
}
