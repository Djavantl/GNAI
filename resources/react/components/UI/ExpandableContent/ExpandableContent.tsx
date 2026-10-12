import { useEffect, useId, useRef, useState, type ReactNode } from "react";
import "./ExpandableContent.css";

interface ExpandableContentProps {
    children: ReactNode;
    collapsedHeight?: number;
    expandLabel?: string;
    collapseLabel?: string;
}

export default function ExpandableContent({
    children,
    collapsedHeight = 144,
    expandLabel = "Ver mais",
    collapseLabel = "Ver menos",
}: ExpandableContentProps): React.JSX.Element {
    const contentId = useId();
    const contentRef = useRef<HTMLDivElement>(null);
    const [expanded, setExpanded] = useState(false);
    const [canExpand, setCanExpand] = useState(false);

    useEffect(() => {
        const content = contentRef.current;
        if (!content) return;

        const measure = (): void => {
            const overflows = content.scrollHeight > collapsedHeight + 1;
            setCanExpand(overflows);
            if (!overflows) setExpanded(false);
        };

        measure();
        if (typeof ResizeObserver === "undefined") {
            window.addEventListener("resize", measure);
            return () => window.removeEventListener("resize", measure);
        }

        const observer = new ResizeObserver(measure);
        observer.observe(content);
        return () => observer.disconnect();
    }, [children, collapsedHeight]);

    return (
        <div
            className={`expandableContent${expanded ? " expandableContent--expanded" : ""}${canExpand ? " expandableContent--overflowing" : ""}`}
        >
            <div
                id={contentId}
                className="expandableContent__viewport"
                style={
                    !expanded
                        ? { maxHeight: `${collapsedHeight}px` }
                        : undefined
                }
            >
                <div ref={contentRef} className="expandableContent__body">
                    {children}
                </div>
            </div>
            {canExpand ? (
                <button
                    type="button"
                    className="expandableContent__toggle"
                    aria-controls={contentId}
                    aria-expanded={expanded}
                    onClick={() => setExpanded((value) => !value)}
                >
                    <span>{expanded ? collapseLabel : expandLabel}</span>
                    <i
                        className={`fa fa-angle-${expanded ? "up" : "down"}`}
                        aria-hidden="true"
                    />
                </button>
            ) : null}
        </div>
    );
}
