import PublicationCard, { ScheduleStatus } from "@/components/Website/PublicationCard/PublicationCard";
import SiteLayout from "@/components/Website/SiteLayout/SiteLayout";
import SiteLink from "@/components/Website/SiteLayout/SiteLink";
import SitePageHeader from "@/components/Website/SitePageHeader/SitePageHeader";
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
                eyebrow={publication.type_label}
                title={publication.title}
                subtitle={publication.summary}
                trail={[
                    { label: "Publicações", href: websiteRoutes.publications },
                    { label: publication.type_label },
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
                    {publication.cover_url ? (
                        <figure className="websitePublic__cover">
                            <img src={publication.cover_url} alt={publication.cover_alt} />
                        </figure>
                    ) : null}
                    <div className="websitePublic__articleMain">
                        {publication.content_html ? (
                            <RichTextContent html={publication.content_html} className="websitePublic__content" />
                        ) : null}
                    </div>
                    {schedule || publication.link_url || publication.organizer || publication.audience ? (
                        <section className="websitePublic__aside" aria-labelledby="publication-details">
                            <h2 id="publication-details" className="websitePublic__blockTitle">Sobre esta publicação</h2>
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
                        </section>
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
                            <h2 id="publication-speakers" className="websitePublic__blockTitle">Participantes</h2>
                            <ul className="websitePublic__participants">
                                {publication.speakers.map((speaker) => (
                                    <li key={speaker.id}>
                                        <img src={speaker.photo_url} alt={`Foto de ${speaker.name}`} loading="lazy" />
                                        <span>
                                            <strong>{speaker.name}</strong>
                                            {speaker.role ? <span>, {speaker.role}</span> : null}
                                        </span>
                                    </li>
                                ))}
                            </ul>
                        </section>
                    ) : null}
                    {publication.tags.length ? (
                        <div className="websitePublic__tags" aria-label="Tags da publicação">
                            <strong>Tags</strong>
                            {publication.tags.map((tag) => (
                                <SiteLink
                                    key={tag}
                                    href={`${websiteRoutes.publications}?tag=${encodeURIComponent(tag)}`}
                                    className="websitePublic__tag"
                                >
                                    {tag}
                                </SiteLink>
                            ))}
                        </div>
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
