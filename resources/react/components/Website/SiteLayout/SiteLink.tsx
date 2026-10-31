import { createContext, useContext, type ReactNode } from "react";
import { Link } from "@inertiajs/react";

/** Indica se o site está sendo renderizado dentro da prévia do construtor. */
export const SitePreviewContext = createContext(false);

export function useSitePreview(): boolean {
    return useContext(SitePreviewContext);
}

function isInternal(href: string): boolean {
    return href.startsWith("/") && !href.startsWith("//");
}

/**
 * Link do site público: rotas internas usam a navegação do Inertia, links
 * externos abrem em nova aba. Na prévia do construtor, os cliques não
 * navegam para não descartar a edição em andamento.
 */
export default function SiteLink({
    href,
    className,
    children,
    ariaLabel,
    ariaCurrent,
}: {
    href: string;
    className?: string;
    children: ReactNode;
    ariaLabel?: string;
    ariaCurrent?: "page";
}): React.JSX.Element {
    const preview = useSitePreview();

    if (preview) {
        return (
            <a
                href={href}
                className={className}
                aria-label={ariaLabel}
                aria-current={ariaCurrent}
                onClick={(event) => event.preventDefault()}
            >
                {children}
            </a>
        );
    }

    if (isInternal(href) && !href.includes("#")) {
        return (
            <Link
                href={href}
                className={className}
                aria-label={ariaLabel}
                aria-current={ariaCurrent}
            >
                {children}
            </Link>
        );
    }

    const external = /^https?:\/\//i.test(href);

    return (
        <a
            href={href}
            className={className}
            aria-label={ariaLabel}
            target={external ? "_blank" : undefined}
            rel={external ? "noopener noreferrer" : undefined}
        >
            {children}
        </a>
    );
}
