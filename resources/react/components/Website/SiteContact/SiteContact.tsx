import type { CSSProperties } from "react";
import SiteCollection from "../SiteCollection/SiteCollection";
import { SiteSocialLinks } from "../SiteLayout/SiteLayout";
import { appearanceColor as color } from "../WebsiteAppearance";
import type { WebsiteSection, WebsiteSite } from "../Website.types";
import "./SiteContact.css";

export default function SiteContact({ section, site }: { section: WebsiteSection; site: WebsiteSite }): React.JSX.Element | null {
    const appearance = section.contact;
    if (!appearance) return null;

    const channels = [
        site.address ? { icon: "fa fa-map-marker", label: "Endereço", value: site.address, href: undefined } : null,
        site.contact_phone ? {
            icon: "fa fa-phone", label: "Telefone", value: site.contact_phone,
            href: `tel:${site.contact_phone.replace(/[^\d+]/g, "")}`,
        } : null,
        site.contact_email ? {
            icon: "fa fa-envelope-o", label: "E-mail", value: site.contact_email, href: `mailto:${site.contact_email}`,
        } : null,
    ].filter((channel): channel is NonNullable<typeof channel> => channel !== null);
    const style = {
        "--contact-card-align": appearance.card_align,
        "--contact-card-radius": `${appearance.card_radius}px`,
        "--contact-card-padding": `${appearance.card_padding}px`,
        "--contact-card-background": color(appearance.card_background, "#ffffff"),
        "--contact-card-border": color(appearance.card_border_color, "var(--site-border)"),
        "--contact-title-color": color(appearance.card_title_color, "var(--site-ink)"),
        "--contact-title-size": `${appearance.card_title_size}px`,
        "--contact-text-color": color(appearance.card_text_color, "var(--site-primary)"),
        "--contact-text-size": `${appearance.card_text_size}px`,
        "--contact-icon-size": `${appearance.icon_size}px`,
        "--contact-icon-radius": `${appearance.icon_radius}px`,
        "--contact-icon-border-width": `${appearance.icon_border_width}px`,
        "--contact-icon-color": color(appearance.icon_color, "var(--site-primary)"),
        "--contact-icon-background": color(appearance.icon_background, "var(--site-primary-soft)"),
    } as CSSProperties;

    return (
        <SiteCollection
            section={section}
            appearance={appearance}
            className="siteContact"
            style={style}
            emptyLabel="Para orientação sobre o núcleo, procure o setor pedagógico ou os canais oficiais de sua instituição."
            fallbackTitle="Contato e localização"
            itemLabel="canal"
            cards={channels.map((channel, index) => ({
                id: index,
                label: channel.label,
                content: (
                    <article className="siteContact__card">
                        {appearance.show_icons ? (
                            <span className="siteContact__icon" aria-hidden="true">
                                <i className={channel.icon} />
                            </span>
                        ) : null}
                        <h3 className="siteContact__label">{channel.label}</h3>
                        {channel.href ? <a href={channel.href}>{channel.value}</a> : <p>{channel.value}</p>}
                    </article>
                ),
            }))}
        >
            {appearance.show_social ? <SiteSocialLinks site={site} className="siteContact__social" /> : null}
        </SiteCollection>
    );
}
