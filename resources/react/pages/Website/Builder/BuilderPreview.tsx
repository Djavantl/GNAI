import { useEffect, useLayoutEffect, useRef, useState } from "react";
import Button from "@/components/UI/Button/Button";
import SiteLayout from "@/components/Website/SiteLayout/SiteLayout";
import SiteSections from "@/components/Website/SiteSections/SiteSections";
import type {
    WebsiteContent,
    WebsiteSection,
    WebsiteSite,
} from "@/components/Website/Website.types";
import { IDENTITY_ID, type BuilderEditTarget } from "./websiteBuilder";

type Device = "desktop" | "mobile";

const frameWidths: Record<Device, number> = { desktop: 1280, mobile: 390 };

type PreviewRule = {
    selector: string;
    /** Campos da identidade ficam no topo/rodapé, mesmo quando exibidos dentro de uma seção. */
    identity?: boolean;
    field: (element: Element) => string | undefined;
};

function itemIndex(element: Element, itemSelector: string): number {
    const item = element.closest(itemSelector);

    return item?.parentElement ? Array.from(item.parentElement.children).indexOf(item) : -1;
}

function itemField(itemSelector: string, name: string): (element: Element) => string | undefined {
    return (element) => {
        const index = itemIndex(element, itemSelector);

        return index >= 0 ? `item-${index}-${name}` : undefined;
    };
}

function contactField(element: Element): string {
    const item = element.closest("li") ?? element;

    if (item.querySelector('a[href^="tel:"]') || element.matches('a[href^="tel:"]')) return "contact_phone";
    if (item.querySelector('a[href^="mailto:"]') || element.matches('a[href^="mailto:"]')) return "contact_email";

    return "address";
}

/**
 * Elementos da prévia que levam a um campo específico ao receberem clique
 * duplo. Vence o menor elemento sob o ponteiro; fora deles, abre a seção
 * (ou a identidade, no topo e no rodapé) sem campo específico.
 */
const previewRules: PreviewRule[] = [
    { selector: ".siteHero__title, .siteSections__title", field: () => "title" },
    { selector: ".siteHero__text, .siteSections__subtitle", field: () => "subtitle" },
    { selector: ".siteSections__button, .siteSections__guidanceLink, .siteHero__button", field: () => "button_label" },
    { selector: ".siteHero__badge", field: () => "hero-badge_text" },
    {
        selector: ".siteHero__image",
        field: (element) => {
            const id = element.getAttribute("data-hero-slide-id");
            return id && id !== "background" ? `slide-${id}-image` : "image";
        },
    },
    { selector: ".siteSections__richText", field: () => "body" },
    { selector: ".siteSections__aboutImage, .siteHero__media", field: () => "image" },
    { selector: ".siteSections__resourceLink", field: itemField(".siteSections__feature", "url") },
    { selector: ".siteSections__question summary", field: itemField(".siteSections__question", "title") },
    { selector: ".siteSections__question p", field: itemField(".siteSections__question", "text") },
    { selector: ".siteSections__step h3", field: itemField(".siteSections__step", "title") },
    { selector: ".siteSections__step p", field: itemField(".siteSections__step", "text") },
    { selector: ".siteSections__feature, .siteSections__feature h3", field: itemField(".siteSections__feature", "title") },
    { selector: ".siteSections__feature p", field: itemField(".siteSections__feature", "text") },
    { selector: ".siteSections__feature .siteSections__featureIcon", field: itemField(".siteSections__feature", "icon") },
    { selector: ".siteSections__grid", field: () => "limit" },
    { selector: ".siteLayout__brandName, .siteLayout__footerName", identity: true, field: () => "site_name" },
    { selector: ".siteLayout__brandTagline", identity: true, field: () => "tagline" },
    { selector: ".siteLayout__logo, .siteLayout__monogram", identity: true, field: () => "logo" },
    { selector: ".siteLayout__footerBrand > p:not(.siteLayout__footerName)", identity: true, field: () => "footer_text" },
    { selector: ".siteLayout__social", identity: true, field: () => "instagram_url" },
    {
        selector: ".siteLayout__footerContact span, .siteLayout__footerContact a, .siteSections__contact > li",
        identity: true,
        field: contactField,
    },
];

const previewSelector = previewRules.map((rule) => rule.selector).join(", ");

function contains(rect: DOMRect, x: number, y: number): boolean {
    return x >= rect.left && x <= rect.right && y >= rect.top && y <= rect.bottom;
}

/**
 * Descobre o que editar a partir de um ponto da prévia. A prévia é inerte,
 * então o clique chega ao contêiner e a busca é feita pela geometria.
 */
function resolvePreviewTarget(frame: HTMLElement, x: number, y: number): BuilderEditTarget | null {
    let element: Element | undefined;
    let smallestArea = Infinity;

    for (const candidate of Array.from(frame.querySelectorAll(previewSelector))) {
        const rect = candidate.getBoundingClientRect();
        const area = rect.width * rect.height;

        // Em empate, o descendente (que vem depois na ordem do documento) vence.
        if (area && contains(rect, x, y) && area <= smallestArea) {
            element = candidate;
            smallestArea = area;
        }
    }

    const blocks = frame.querySelectorAll<HTMLElement>("[data-section-id], .siteLayout__header, .siteLayout__footer");
    const container = Array.from(blocks).find((block) => contains(block.getBoundingClientRect(), x, y));
    const match = element;
    const rule = match ? previewRules.find((candidate) => match.matches(candidate.selector)) : undefined;

    if (match && rule) {
        if (rule.identity) return { sectionId: IDENTITY_ID, field: rule.field(match) };

        const sectionId = match.closest<HTMLElement>("[data-section-id]")?.dataset.sectionId;

        if (sectionId) return { sectionId, field: rule.field(match) };
    }

    if (!container) return null;

    return container.dataset.sectionId ? { sectionId: container.dataset.sectionId } : { sectionId: IDENTITY_ID };
}

/**
 * Prévia em tempo real da página inicial. O site é renderizado na largura
 * real do dispositivo e reduzido para caber no painel; a prévia é inerte
 * (sem foco ou navegação), pois a edição acontece pelos campos ao lado.
 * O clique duplo em um elemento abre o campo correspondente no editor.
 */
export default function BuilderPreview({
    site,
    sections,
    content,
    selectedId,
    onEdit,
}: {
    site: WebsiteSite;
    sections: WebsiteSection[];
    content: WebsiteContent;
    selectedId: string | null;
    onEdit: (target: BuilderEditTarget) => void;
}): React.JSX.Element {
    const panel = useRef<HTMLElement>(null);
    const viewport = useRef<HTMLDivElement>(null);
    const frame = useRef<HTMLDivElement>(null);
    const keepScroll = useRef(false);
    const [panelHeight, setPanelHeight] = useState<number>();
    const [device, setDevice] = useState<Device>("desktop");
    const [viewportWidth, setViewportWidth] = useState(0);
    const [frameHeight, setFrameHeight] = useState(0);
    const frameWidth = frameWidths[device];
    const scale = viewportWidth ? Math.min(1, viewportWidth / frameWidth) : 1;

    useLayoutEffect(() => {
        const desktop = window.matchMedia("(min-width: 1101px)");
        let frame = 0;

        const measure = (): void => {
            frame = 0;
            if (!desktop.matches || !panel.current) {
                setPanelHeight(undefined);
                return;
            }

            const actions = document.querySelector(".websiteBuilderPage .formActions__bar");
            const bottomGap = (actions?.getBoundingClientRect().height ?? 60) + 16;
            const stickyTop = Number.parseFloat(window.getComputedStyle(panel.current).top) || 16;
            const top = Math.max(stickyTop, panel.current.getBoundingClientRect().top);
            setPanelHeight(Math.max(180, Math.floor(window.innerHeight - top - bottomGap)));
        };
        const schedule = (): void => {
            if (!frame) frame = window.requestAnimationFrame(measure);
        };

        measure();
        window.addEventListener("scroll", schedule, { passive: true });
        window.addEventListener("resize", schedule);
        const observer = new ResizeObserver(schedule);
        if (panel.current) observer.observe(panel.current);

        return () => {
            window.cancelAnimationFrame(frame);
            window.removeEventListener("scroll", schedule);
            window.removeEventListener("resize", schedule);
            observer.disconnect();
        };
    }, []);

    useLayoutEffect(() => {
        const viewportElement = viewport.current;
        const frameElement = frame.current;

        if (!viewportElement || !frameElement || typeof ResizeObserver === "undefined") return;

        const observer = new ResizeObserver(() => {
            setViewportWidth(viewportElement.clientWidth);
            setFrameHeight(frameElement.offsetHeight);
        });

        observer.observe(viewportElement);
        observer.observe(frameElement);

        return () => observer.disconnect();
    }, []);

    useEffect(() => {
        // Seleção feita na própria prévia: o usuário já está vendo o elemento.
        if (keepScroll.current) {
            keepScroll.current = false;
            return;
        }

        if (!selectedId || !viewport.current || !frame.current) return;

        if (selectedId === IDENTITY_ID) {
            viewport.current.scrollTo({ top: 0, behavior: "smooth" });
            return;
        }

        const target = frame.current.querySelector<HTMLElement>(`[data-section-id="${CSS.escape(selectedId)}"]`);

        if (!target) return;

        viewport.current.scrollTo({ top: Math.max(0, target.offsetTop * scale - 24), behavior: "smooth" });
    }, [selectedId, scale, device]);

    function handleDoubleClick(event: React.MouseEvent<HTMLDivElement>): void {
        if (!frame.current) return;

        const target = resolvePreviewTarget(frame.current, event.clientX, event.clientY);

        if (!target) return;

        event.preventDefault();
        window.getSelection()?.removeAllRanges();
        keepScroll.current = target.sectionId !== selectedId;
        onEdit(target);
    }

    return (
        <section
            ref={panel}
            className="websiteBuilder__preview"
            aria-labelledby="website-preview-title"
            style={panelHeight ? { height: panelHeight } : undefined}
        >
            <div className="websiteBuilder__previewBar">
                <h2 id="website-preview-title" className="websiteBuilder__previewTitle">
                    <i className="fa fa-eye" aria-hidden="true" /> Prévia em tempo real
                </h2>
                <div className="websiteBuilder__devices" role="group" aria-label="Dispositivo da prévia">
                    <Button
                        size="sm"
                        tone="inverse"
                        className="websiteBuilder__device"
                        leftIconClass="fa fa-desktop"
                        label="Computador"
                        aria-pressed={device === "desktop"}
                        onClick={() => setDevice("desktop")}
                    />
                    <Button
                        size="sm"
                        tone="inverse"
                        className="websiteBuilder__device"
                        leftIconClass="fa fa-mobile"
                        label="Celular"
                        aria-pressed={device === "mobile"}
                        onClick={() => setDevice("mobile")}
                    />
                </div>
            </div>
            <div
                ref={viewport}
                className="websiteBuilder__viewport"
                onDoubleClick={handleDoubleClick}
            >
                <div
                    className="websiteBuilder__frameSizer"
                    style={{ height: frameHeight ? frameHeight * scale : undefined }}
                >
                    <div
                        ref={frame}
                        className="websiteBuilder__frame"
                        style={{ width: frameWidth, transform: `scale(${scale})` }}
                        inert
                    >
                        <SiteLayout site={site} preview>
                            <SiteSections
                                sections={sections}
                                site={site}
                                content={content}
                                highlightedId={selectedId}
                            />
                        </SiteLayout>
                    </div>
                </div>
            </div>
        </section>
    );
}
