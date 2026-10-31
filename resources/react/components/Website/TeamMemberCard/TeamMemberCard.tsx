import type { TeamMemberData } from "../Website.types";
import "./TeamMemberCard.css";

export default function TeamMemberCard({
    member,
    headingLevel = 3,
}: {
    member: TeamMemberData;
    headingLevel?: 2 | 3 | 4;
}): React.JSX.Element {
    const Heading = `h${headingLevel}` as "h2" | "h3" | "h4";

    return (
        <article className="teamMemberCard">
            <img
                src={member.photo_url}
                alt={`Foto de ${member.name}`}
                className="teamMemberCard__photo"
                loading="lazy"
            />
            <Heading className="teamMemberCard__name">{member.name}</Heading>
            {member.role ? <p className="teamMemberCard__role">{member.role}</p> : null}
        </article>
    );
}
