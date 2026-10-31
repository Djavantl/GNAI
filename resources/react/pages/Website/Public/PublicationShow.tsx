import PublicationCard, { ScheduleStatus } from "@/components/Website/PublicationCard/PublicationCard";
import SiteLayout from "@/components/Website/SiteLayout/SiteLayout";
import SiteLink from "@/components/Website/SiteLayout/SiteLink";
import SitePageHeader from "@/components/Website/SitePageHeader/SitePageHeader";
import TeamMemberCard from "@/components/Website/TeamMemberCard/TeamMemberCard";
import {
    websiteRoutes,
    type PublicationCardData,
    type PublicationDetailData,
    type WebsiteSeo,
    type WebsiteSite,
} from "@/components/Website/Website.types";
import ImageLightbox from "@/components/UI/ImageLightbox/ImageLightbox";
import RichTextContent from "@/components/UI/RichTextContent/RichTextContent";
import "./WebsitePublic.css";

type Props = {
    site: WebsiteSite;
    publication: PublicationDetailData;
    related: PublicationCardData[];
    seo: WebsiteSeo;
};

export default function WebsitePublicPublicationShowPage({
    site,
    publication,
    related,
    seo,
}: Props): React.JSX.Element {
    const schedule = publication.schedule;

    return (
        <SiteLayout site={site} current="publications" seo={seo}>
            <SitePageHeader
                eyebrow={[publication.type_label, publication.category].filter(Boolean).join(" · ")}
                title={publication.title}
                subtitle={publication.summary}
                trail={[
                    { label: "Publicações", href: websiteRoutes.publications },
                    { label: publication.title },
                ]}
            >
                <p className="websitePublic__byline">
                    {publication.published_at_label ? (
                        <span>
                            <i className="fa fa-calendar-o" aria-hidden="true" />
                            {" "}Publicado em{" "}
                            <time dateTime={publication.published_at ?? undefined}>{publication.published_at_label}</time>
                        </span>
                    ) : null}
                    {publication.author ? (
                        <span>
                            <i className="fa fa-user" aria-hidden="true" /> Por {publication.author}
                        </span>
                    ) : null}
                </p>
            </SitePageHeader>
            <article className="websitePublic__section">
                <div className="siteLayout__container websitePublic__article">
                    <div className="websitePublic__articleMain">
                        {publication.cover_url ? (
                            <figure className="websitePublic__cover">
                                <img src={publication.cover_url} alt={publication.cover_alt} />
                            </figure>
                        ) : null}
                        {publication.content_html ? (
                            <RichTextContent html={publication.content_html} className="websitePublic__content" />
                        ) : null}
                        {publication.gallery.length ? (
                            <section className="websitePublic__block" aria-labelledby="publication-gallery">
                                <h2 id="publication-gallery" className="websitePublic__blockTitle">Galeria de fotos</h2>
                                <ImageLightbox className="websitePublic__gallery">
                                    {publication.gallery.map((image) => (
                                        <a
                                            key={image.id}
                                            href={image.url}
                                            data-lightbox
                                            title={image.alt}
                                            aria-label={`Ampliar foto: ${image.alt}`}
                                        >
                                            <img src={image.url} alt={image.alt} loading="lazy" />
                                        </a>
                                    ))}
                                </ImageLightbox>
                            </section>
                        ) : null}
                        {publication.speakers.length ? (
                            <section className="websitePublic__block" aria-labelledby="publication-speakers">
                                <h2 id="publication-speakers" className="websitePublic__blockTitle">Palestrantes</h2>
                                <ul className="websitePublic__teamGrid websitePublic__teamGrid--compact">
                                    {publication.speakers.map((speaker) => (
                                        <li key={speaker.id}>
                                            <TeamMemberCard member={speaker} />
                                        </li>
                                    ))}
                                </ul>
                            </section>
                        ) : null}
                    </div>
                    {schedule || publication.link_url || publication.organizer || publication.audience ? (
                        <aside className="websitePublic__aside" aria-label="Informações do evento">
                            {schedule ? (
                                <>
                                    <ScheduleStatus schedule={schedule} />
                                    <dl className="websitePublic__facts">
                                        <div>
                                            <dt><i className="fa fa-calendar" aria-hidden="true" /> Data</dt>
                                            <dd>{schedule.date_label}</dd>
                                        </div>
                                        {schedule.time_label ? (
                                            <div>
                                                <dt><i className="fa fa-clock-o" aria-hidden="true" /> Horário</dt>
                                                <dd>{schedule.time_label}</dd>
                                            </div>
                                        ) : null}
                                        {schedule.location ? (
                                            <div>
                                                <dt><i className="fa fa-map-marker" aria-hidden="true" /> Local</dt>
                                                <dd>{schedule.location}</dd>
                                            </div>
                                        ) : null}
                                        {publication.organizer ? (
                                            <div>
                                                <dt><i className="fa fa-sitemap" aria-hidden="true" /> Organização</dt>
                                                <dd>{publication.organizer}</dd>
                                            </div>
                                        ) : null}
                                        {publication.audience ? (
                                            <div>
                                                <dt><i className="fa fa-users" aria-hidden="true" /> Público</dt>
                                                <dd>{publication.audience}</dd>
                                            </div>
                                        ) : null}
                                    </dl>
                                </>
                            ) : (
                                <dl className="websitePublic__facts">
                                    {publication.organizer ? (
                                        <div>
                                            <dt><i className="fa fa-sitemap" aria-hidden="true" /> Organização</dt>
                                            <dd>{publication.organizer}</dd>
                                        </div>
                                    ) : null}
                                    {publication.audience ? (
                                        <div>
                                            <dt><i className="fa fa-users" aria-hidden="true" /> Público</dt>
                                            <dd>{publication.audience}</dd>
                                        </div>
                                    ) : null}
                                </dl>
                            )}
                            {publication.link_url ? (
                                <SiteLink href={publication.link_url} className="websitePublic__button websitePublic__button--block">
                                    <i className="fa fa-external-link" aria-hidden="true" />
                                    {publication.link_label || "Acessar link"}
                                </SiteLink>
                            ) : null}
                        </aside>
                    ) : null}
                </div>
            </article>
            {related.length ? (
                <section className="websitePublic__section websitePublic__section--tinted" aria-labelledby="related-publications">
                    <div className="siteLayout__container">
                        <h2 id="related-publications" className="websitePublic__blockTitle">Veja também</h2>
                        <div className="websitePublic__grid">
                            {related.map((item) => (
                                <PublicationCard key={item.id} publication={item} />
                            ))}
                        </div>
                    </div>
                </section>
            ) : null}
        </SiteLayout>
    );
}
