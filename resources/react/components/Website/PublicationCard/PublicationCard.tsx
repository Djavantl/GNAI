import { joinClassNames } from "@/utils/joinClassNames";
import SiteLink from "../SiteLayout/SiteLink";
import type { PublicationCardData, PublicationSchedule, PublicationsAppearance } from "../Website.types";
import "./PublicationCard.css";

export function ScheduleStatus({
    schedule,
}: {
    schedule: PublicationSchedule;
}): React.JSX.Element {
    const icon = schedule.status === "ongoing"
        ? "fa fa-circle"
        : schedule.status === "upcoming"
            ? "fa fa-clock-o"
            : "fa fa-check";

    return (
        <span className={`publicationCard__status publicationCard__status--${schedule.status}`}>
            <i className={icon} aria-hidden="true" />
            {schedule.status_label}
        </span>
    );
}

/**
 * Cartão de publicação do site. Eventos destacam o período e a situação
 * (em breve, acontecendo agora, encerrado) também por texto e ícone.
 */
export default function PublicationCard({
    publication,
    headingLevel = 3,
    variant = "default",
    appearance,
}: {
    publication: PublicationCardData;
    headingLevel?: 2 | 3;
    variant?: "default" | "event";
    appearance?: PublicationsAppearance;
}): React.JSX.Element {
    const Heading = headingLevel === 2 ? "h2" : "h3";
    const schedule = publication.schedule;

    if (variant === "event" && schedule) {
        return (
            <article className="publicationCard publicationCard--event">
                {appearance?.show_date !== false ? (
                    <div className="publicationCard__dateBadge" aria-hidden="true">
                        <span className="publicationCard__day">{schedule.day}</span>
                        <span className="publicationCard__month">{schedule.month}</span>
                    </div>
                ) : null}
                <div className="publicationCard__body">
                    {appearance?.show_tags !== false ? <ScheduleStatus schedule={schedule} /> : null}
                    <Heading className="publicationCard__title">
                        <SiteLink href={publication.url}>{publication.title}</SiteLink>
                    </Heading>
                    <ul className="publicationCard__meta">
                        {appearance?.show_date !== false ? (
                            <li>
                                <i className="fa fa-calendar" aria-hidden="true" />
                                <span className="publicationCard__srOnly">Data: </span>
                                {schedule.date_label}
                            </li>
                        ) : null}
                        {schedule.time_label ? (
                            <li>
                                <i className="fa fa-clock-o" aria-hidden="true" />
                                <span className="publicationCard__srOnly">Horário: </span>
                                {schedule.time_label}
                            </li>
                        ) : null}
                        {schedule.location ? (
                            <li>
                                <i className="fa fa-map-marker" aria-hidden="true" />
                                <span className="publicationCard__srOnly">Local: </span>
                                {schedule.location}
                            </li>
                        ) : null}
                    </ul>
                </div>
            </article>
        );
    }

    return (
        <article
            className={joinClassNames(
                "publicationCard",
                publication.is_featured && "publicationCard--featured",
            )}
        >
            {appearance?.show_image !== false ? (
                <div className="publicationCard__media">
                    {publication.cover_url ? (
                        <img src={publication.cover_url} alt={publication.cover_alt} loading="lazy" />
                    ) : (
                        <span className="publicationCard__placeholder" aria-hidden="true">
                            <i className={publication.schedule ? "fa fa-calendar" : "fa fa-file-text-o"} />
                        </span>
                    )}
                </div>
            ) : null}
            <div className="publicationCard__body">
                {appearance?.show_tags !== false ? (
                    <div className="publicationCard__tags">
                        <span className="publicationCard__type">{publication.type_label}</span>
                        {publication.tags.map((tag) => (
                            <span key={tag} className="publicationCard__category">{tag}</span>
                        ))}
                        {schedule ? <ScheduleStatus schedule={schedule} /> : null}
                    </div>
                ) : null}
                {appearance?.show_date !== false && publication.published_at_label ? (
                    <time className="publicationCard__date" dateTime={publication.published_at ?? undefined}>
                        {publication.published_at_label}
                    </time>
                ) : null}
                <Heading className="publicationCard__title">
                    <SiteLink href={publication.url}>{publication.title}</SiteLink>
                </Heading>
                {appearance?.show_summary !== false && publication.summary ? (
                    <p className="publicationCard__summary">{publication.summary}</p>
                ) : null}
                <SiteLink
                    href={publication.url}
                    className="publicationCard__more"
                    ariaLabel={`Ler a publicação ${publication.title}`}
                >
                    Ler publicação <i className="fa fa-arrow-right" aria-hidden="true" />
                </SiteLink>
            </div>
        </article>
    );
}
