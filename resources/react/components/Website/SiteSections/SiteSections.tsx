import { isWebsiteSectionVisible } from "../WebsiteSections";
import SitePublications from "../SitePublications/SitePublications";
import SiteGuidance from "../SiteGuidance/SiteGuidance";
import SiteContact from "../SiteContact/SiteContact";
import { joinClassNames } from "@/utils/joinClassNames";
import SiteHero from "../SiteHero/SiteHero";
import SiteAbout from "../SiteAbout/SiteAbout";
import SitePillars from "../SitePillars/SitePillars";
import { useSitePreview } from "../SiteLayout/SiteLink";
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

const renderers = {
    hero: SiteHero,
    about: SiteAbout,
    pillars: SitePillars,
    audience: SitePillars,
    guidance: SiteGuidance,
    faq: SitePillars,
    resources: SitePillars,
    features: SitePillars,
    team: SiteTeam,
    events: SitePublications,
    publications: SitePublications,
    contact: SiteContact,
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
