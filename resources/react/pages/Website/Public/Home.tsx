import SiteLayout from "@/components/Website/SiteLayout/SiteLayout";
import SiteSections from "@/components/Website/SiteSections/SiteSections";
import type {
    WebsiteContent,
    WebsiteSection,
    WebsiteSeo,
    WebsiteSite,
} from "@/components/Website/Website.types";

type Props = {
    site: WebsiteSite;
    sections: WebsiteSection[];
    content: WebsiteContent;
    seo: WebsiteSeo;
};

export default function WebsitePublicHomePage({
    site,
    sections,
    content,
    seo,
}: Props): React.JSX.Element {
    return (
        <SiteLayout site={site} current="home" seo={seo}>
            <SiteSections sections={sections} site={site} content={content} />
        </SiteLayout>
    );
}
