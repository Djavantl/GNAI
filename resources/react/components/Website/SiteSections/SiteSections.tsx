import type { CSSProperties } from "react";
import RichTextContent from "@/components/UI/RichTextContent/RichTextContent";
import { joinClassNames } from "@/utils/joinClassNames";
import PublicationCard from "../PublicationCard/PublicationCard";
import { SiteSocialLinks } from "../SiteLayout/SiteLayout";
import SiteLink from "../SiteLayout/SiteLink";
import TeamMemberCard from "../TeamMemberCard/TeamMemberCard";
import {
    websiteRoutes,
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

function HeroSection({ section, site, isMainHeading }: SectionProps): React.JSX.Element {
    const Heading = isMainHeading ? "h1" : "h2";
    const style = section.image_url
        ? ({ "--site-hero-image": `url("${section.image_url}")` } as CSSProperties)
        : undefined;

    return (
        <section
            className={joinClassNames("siteSections__hero", section.image_url ? "siteSections__hero--image" : undefined)}
            style={style}
            aria-label={section.title ? undefined : site.name}
        >
            <div className="siteLayout__container siteSections__heroInner">
                {section.title ? <p className="siteSections__heroEyebrow">{site.name}</p> : null}
                <Heading className="siteSections__heroTitle">{section.title || site.name}</Heading>
                {section.subtitle ? <p className="siteSections__heroText">{section.subtitle}</p> : null}
                {section.button_label && section.button_url ? (
                    <SiteLink href={section.button_url} className="siteSections__button siteSections__button--light">
                        {section.button_label}
                    </SiteLink>
                ) : null}
            </div>
        </section>
    );
}

function AboutSection({ section }: SectionProps): React.JSX.Element {
    return (
        <section className="siteSections__section">
            <div
                className={joinClassNames(
                    "siteLayout__container siteSections__about",
                    section.image_url ? "siteSections__about--withImage" : undefined,
                )}
            >
                <div>
                    <SectionHeading section={section} align="start" />
                    {section.body ? (
                        <RichTextContent html={section.body} className="siteSections__richText" />
                    ) : null}
                    {section.button_label && section.button_url ? (
                        <SiteLink href={section.button_url} className="siteSections__button">
                            {section.button_label}
                        </SiteLink>
                    ) : null}
                </div>
                {section.image_url ? (
                    <img src={section.image_url} alt={section.image_alt} className="siteSections__aboutImage" />
                ) : null}
            </div>
        </section>
    );
}

function HighlightsSection({ section }: SectionProps): React.JSX.Element {
    return (
        <section className="siteSections__section siteSections__section--tinted">
            <div className="siteLayout__container">
                <SectionHeading section={section} />
                <ul className="siteSections__highlights">
                    {section.items.map((item, index) => (
                        <li key={`${item.title}-${index}`}>
                            <span className="siteSections__highlightValue">{item.value}</span>
                            <span className="siteSections__highlightLabel">{item.title}</span>
                        </li>
                    ))}
                </ul>
            </div>
        </section>
    );
}

function FeaturesSection({ section }: SectionProps): React.JSX.Element {
    return (
        <section className="siteSections__section">
            <div className="siteLayout__container">
                <SectionHeading section={section} />
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
                        </li>
                    ))}
                </ul>
            </div>
        </section>
    );
}

function TeamSection({ section, content }: SectionProps): React.JSX.Element {
    const members = content.team.slice(0, section.limit);

    return (
        <section className="siteSections__section siteSections__section--tinted">
            <div className="siteLayout__container">
                <SectionHeading
                    section={section}
                    align="start"
                    action={{ label: "Ver equipe completa", href: websiteRoutes.team }}
                />
                {members.length ? (
                    <div className="siteSections__grid siteSections__grid--team">
                        {members.map((member) => (
                            <TeamMemberCard key={member.id} member={member} />
                        ))}
                    </div>
                ) : (
                    <p className="siteSections__empty">Em breve apresentaremos nossa equipe.</p>
                )}
            </div>
        </section>
    );
}

function EventsSection({ section, content }: SectionProps): React.JSX.Element {
    const events = content.events.slice(0, section.limit);

    return (
        <section className="siteSections__section">
            <div className="siteLayout__container">
                <SectionHeading
                    section={section}
                    align="start"
                    action={{ label: "Ver todos os eventos", href: `${websiteRoutes.publications}?type=event` }}
                />
                {events.length ? (
                    <div className="siteSections__grid siteSections__grid--events">
                        {events.map((event) => (
                            <PublicationCard key={event.id} publication={event} variant="event" />
                        ))}
                    </div>
                ) : (
                    <p className="siteSections__empty">Nenhum evento programado no momento.</p>
                )}
            </div>
        </section>
    );
}

function PublicationsSection({ section, content }: SectionProps): React.JSX.Element {
    const publications = content.publications.slice(0, section.limit);

    return (
        <section className="siteSections__section siteSections__section--tinted">
            <div className="siteLayout__container">
                <SectionHeading
                    section={section}
                    align="start"
                    action={{ label: "Ver todas as publicações", href: websiteRoutes.publications }}
                />
                {publications.length ? (
                    <div className="siteSections__grid">
                        {publications.map((publication) => (
                            <PublicationCard key={publication.id} publication={publication} />
                        ))}
                    </div>
                ) : (
                    <p className="siteSections__empty">Nenhuma publicação disponível no momento.</p>
                )}
            </div>
        </section>
    );
}

function CtaSection({ section }: SectionProps): React.JSX.Element {
    return (
        <section className="siteSections__section">
            <div className="siteLayout__container">
                <div className="siteSections__cta">
                    <div>
                        {section.title ? <h2 className="siteSections__ctaTitle">{section.title}</h2> : null}
                        {section.subtitle ? <p>{section.subtitle}</p> : null}
                    </div>
                    {section.button_label && section.button_url ? (
                        <SiteLink href={section.button_url} className="siteSections__button siteSections__button--light">
                            {section.button_label}
                        </SiteLink>
                    ) : null}
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
                    <p className="siteSections__empty">Informações de contato em breve.</p>
                )}
                <SiteSocialLinks site={site} className="siteSections__contactSocial" />
            </div>
        </section>
    );
}

const renderers = {
    hero: HeroSection,
    about: AboutSection,
    highlights: HighlightsSection,
    features: FeaturesSection,
    team: TeamSection,
    events: EventsSection,
    publications: PublicationsSection,
    cta: CtaSection,
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
    const visible = sections.filter((section) => section.enabled && renderers[section.type]);
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
