import { isWebsiteSectionVisible } from "../WebsiteSections";
import SitePublications from "../SitePublications/SitePublications";
import SiteGuidance from "../SiteGuidance/SiteGuidance";
import { joinClassNames } from "@/utils/joinClassNames";
import SiteHero from "../SiteHero/SiteHero";
import SiteAbout from "../SiteAbout/SiteAbout";
import SitePillars from "../SitePillars/SitePillars";
import { SiteSocialLinks } from "../SiteLayout/SiteLayout";
import SiteLink, { useSitePreview } from "../SiteLayout/SiteLink";
import SiteTeam from "../SiteTeam/SiteTeam";
import {
    type WebsiteContent,
    type WebsiteSection,
    type WebsiteSite,
} from "../Website.types";
import "./SiteSections.css";

type SectionProps = {
    section: WebsiteSection;
    site: WebsiteSite;
    content: WebsiteContent;
    isMainHeading: boolean;
};

function SectionHeading({
    section,
    align = "center",
    action,
}: {
    section: WebsiteSection;
    align?: "center" | "start";
    action?: { label: string; href: string };
}): React.JSX.Element | null {
    if (!section.title && !section.subtitle && !action) return null;

    return (
        <div className={joinClassNames("siteSections__heading", `siteSections__heading--${align}`)}>
            <div>
                {section.title ? <h2 className="siteSections__title">{section.title}</h2> : null}
                {section.subtitle ? <p className="siteSections__subtitle">{section.subtitle}</p> : null}
            </div>
            {action ? (
                <SiteLink href={action.href} className="siteSections__more">
                    {action.label} <i className="fa fa-arrow-right" aria-hidden="true" />
                </SiteLink>
            ) : null}
        </div>
    );
}

function CardsSection({ section }: SectionProps): React.JSX.Element {
    return (
        <section
            className={joinClassNames(
                "siteSections__section",
                section.type === "resources" && "siteSections__section--tinted",
            )}
        >
            <div className="siteLayout__container">
                <SectionHeading section={section} align={section.type === "resources" ? "start" : "center"} />
                <ul className="siteSections__features">
                    {section.items.map((item, index) => (
                        <li key={`${item.title}-${index}`} className="siteSections__feature">
                            {item.icon ? (
                                <span className="siteSections__featureIcon" aria-hidden="true">
                                    <i className={item.icon} />
                                </span>
                            ) : null}
                            <h3>{item.title}</h3>
                            {item.text ? <p>{item.text}</p> : null}
                            {section.type === "resources" && item.url ? (
                                <SiteLink
                                    href={item.url}
                                    className="siteSections__more siteSections__resourceLink"
                                    ariaLabel={`Consultar: ${item.title}`}
                                >
                                    Consultar material <i className="fa fa-arrow-right" aria-hidden="true" />
                                </SiteLink>
                            ) : null}
                        </li>
                    ))}
                </ul>
            </div>
        </section>
    );
}

function FaqSection({ section }: SectionProps): React.JSX.Element {
    const preview = useSitePreview();

    return (
        <section className="siteSections__section">
            <div className="siteLayout__container">
                <SectionHeading section={section} align="start" />
                <div className="siteSections__faq">
                    {section.items.map((item, index) => (
                        <details key={index} className="siteSections__question" open={preview || undefined}>
                            <summary>{item.title}</summary>
                            <p>{item.text}</p>
                        </details>
                    ))}
                </div>
            </div>
        </section>
    );
}

function ContactSection({ section, site }: SectionProps): React.JSX.Element {
    const channels = [
        site.address ? { icon: "fa fa-map-marker", label: "Endereço", value: site.address, href: undefined } : null,
        site.contact_phone
            ? { icon: "fa fa-phone", label: "Telefone", value: site.contact_phone, href: `tel:${site.contact_phone.replace(/[^\d+]/g, "")}` }
            : null,
        site.contact_email
            ? { icon: "fa fa-envelope-o", label: "E-mail", value: site.contact_email, href: `mailto:${site.contact_email}` }
            : null,
    ].filter((channel): channel is NonNullable<typeof channel> => channel !== null);

    return (
        <section className="siteSections__section siteSections__section--tinted">
            <div className="siteLayout__container">
                <SectionHeading section={section} />
                {channels.length ? (
                    <ul className="siteSections__contact">
                        {channels.map((channel) => (
                            <li key={channel.label}>
                                <span className="siteSections__featureIcon" aria-hidden="true">
                                    <i className={channel.icon} />
                                </span>
                                <span className="siteSections__contactLabel">{channel.label}</span>
                                {channel.href ? (
                                    <a href={channel.href}>{channel.value}</a>
                                ) : (
                                    <span>{channel.value}</span>
                                )}
                            </li>
                        ))}
                    </ul>
                ) : (
                    <p className="siteSections__empty">Para orientação sobre o núcleo, procure o setor pedagógico ou os canais oficiais de sua instituição.</p>
                )}
                <SiteSocialLinks site={site} className="siteSections__contactSocial" />
            </div>
        </section>
    );
}

const renderers = {
    hero: SiteHero,
    about: SiteAbout,
    pillars: SitePillars,
    audience: SitePillars,
    guidance: SiteGuidance,
    faq: FaqSection,
    resources: CardsSection,
    features: SitePillars,
    team: SiteTeam,
    events: SitePublications,
    publications: SitePublications,
    contact: ContactSection,
} satisfies Record<WebsiteSection["type"], (props: SectionProps) => React.JSX.Element>;

/**
 * Renderiza as seções habilitadas da página inicial, na ordem definida
 * no construtor. O primeiro banner principal recebe o título da página (h1).
 */
export default function SiteSections({
    sections,
    site,
    content,
    highlightedId,
}: {
    sections: WebsiteSection[];
    site: WebsiteSite;
    content: WebsiteContent;
    highlightedId?: string | null;
}): React.JSX.Element {
    const preview = useSitePreview();
    const visible = sections.filter((section) => renderers[section.type]
        && (preview ? section.enabled : isWebsiteSectionVisible(section, content)));
    const mainHeroId = visible.find((section) => section.type === "hero")?.id;

    return (
        <div className="siteSections">
            {!mainHeroId ? <h1 className="siteSections__srOnly">{site.name}</h1> : null}
            {visible.map((section) => {
                const Renderer = renderers[section.type];

                return (
                    <div
                        key={section.id}
                        className={joinClassNames(
                            "siteSections__block",
                            highlightedId === section.id && "siteSections__block--highlighted",
                        )}
                        data-section-id={section.id}
                        id={section.id}
                    >
                        <Renderer
                            section={section}
                            site={site}
                            content={content}
                            isMainHeading={section.id === mainHeroId}
                        />
                    </div>
                );
            })}
            {!visible.length ? (
                <div className="siteLayout__container siteSections__section">
                    <p className="siteSections__empty">Nenhuma seção visível na página inicial.</p>
                </div>
            ) : null}
        </div>
    );
}
