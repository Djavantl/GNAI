import type { TeamMemberData, TeamAppearance } from "../Website.types";
import "./TeamMemberCard.css";

export default function TeamMemberCard({
    member,
    headingLevel = 3,
    appearance,
}: {
    member: TeamMemberData;
    headingLevel?: 2 | 3 | 4;
    appearance?: TeamAppearance;
}): React.JSX.Element {
    const Heading = `h${headingLevel}` as "h2" | "h3" | "h4";

    return (
        <article className="teamMemberCard">
            {appearance?.show_photo !== false ? (
                <img
                    src={member.photo_url}
                    alt={`Foto de ${member.name}`}
                    className="teamMemberCard__photo"
                    loading="lazy"
                />
            ) : null}
            <Heading className="teamMemberCard__name">{member.name}</Heading>
            {appearance?.show_role !== false && member.role ? <p className="teamMemberCard__role">{member.role}</p> : null}
        </article>
    );
}
