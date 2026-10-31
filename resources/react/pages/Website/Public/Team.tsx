import SiteLayout from "@/components/Website/SiteLayout/SiteLayout";
import SitePageHeader from "@/components/Website/SitePageHeader/SitePageHeader";
import TeamMemberCard from "@/components/Website/TeamMemberCard/TeamMemberCard";
import type {
    TeamMemberData,
    WebsiteSeo,
    WebsiteSite,
} from "@/components/Website/Website.types";
import "./WebsitePublic.css";

type Props = {
    site: WebsiteSite;
    members: TeamMemberData[];
    seo: WebsiteSeo;
};

export default function WebsitePublicTeamPage({
    site,
    members,
    seo,
}: Props): React.JSX.Element {
    return (
        <SiteLayout site={site} current="team" seo={seo}>
            <SitePageHeader
                eyebrow="Equipe"
                title="Nossa equipe"
                subtitle={`Conheça as pessoas que fazem o ${site.name} acontecer no dia a dia.`}
                trail={[{ label: "Equipe" }]}
            />
            <section className="websitePublic__section" aria-label="Integrantes da equipe">
                <div className="siteLayout__container">
                    {members.length ? (
                        <ul className="websitePublic__teamGrid">
                            {members.map((member) => (
                                <li key={member.id}>
                                    <TeamMemberCard member={member} headingLevel={2} />
                                </li>
                            ))}
                        </ul>
                    ) : (
                        <p className="websitePublic__empty">Em breve apresentaremos nossa equipe.</p>
                    )}
                </div>
            </section>
        </SiteLayout>
    );
}
