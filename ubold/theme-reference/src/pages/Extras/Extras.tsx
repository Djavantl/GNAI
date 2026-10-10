import PageBreadcrumb from "@/Components/Navigation/PageBreadcrumb/PageBreadcrumb";
import ProfilePage from "@/pages/ProfilePage/ProfilePage";
import EventTimeline from "@/Components/UI/EventTimeline/EventTimeline";
import Sitemap from "@/Components/UI/Sitemap/Sitemap";
import Invoice from "@/Components/UI/Invoice/Invoice";
import EmailTemplates from "@/Components/UI/EmailTemplates/EmailTemplates";
import StatusPage from "@/Components/UI/StatusPage/StatusPage";
import FaqList from "@/Components/UI/FaqList/FaqList";
import SearchResults from "@/Components/UI/SearchResults/SearchResults";
import FilteredGallery from "@/Components/UI/FilteredGallery/FilteredGallery";
import PricingPlans from "@/Components/UI/PricingPlans/PricingPlans";
import type { ExtrasDefinition } from "./Extras.types";
import "./Extras.css";
export default function Extras({
  page,
}: {
  page: ExtrasDefinition;
}): JSX.Element {
  if (page.kind === "maintenance" || page.kind === "coming-soon")
    return <StatusPage kind={page.kind} />;
  return (
    <section className="extrasPage" aria-label={page.title}>
      {page.kind !== "profile" && (
        <>
          <h4 className="extrasPage__title">{page.title}</h4>
          <PageBreadcrumb
            items={[
              { label: "Ubold", href: "#" },
              { label: "Extras", href: "#" },
              { label: page.title },
            ]}
          />
        </>
      )}
      {page.kind === "profile" && <ProfilePage />}
      {page.kind === "timeline" && <EventTimeline />}
      {page.kind === "sitemap" && <Sitemap />}
      {page.kind === "invoice" && <Invoice />}
      {page.kind === "email-template" && <EmailTemplates />}
      {page.kind === "faq" && <FaqList />}
      {page.kind === "search-result" && <SearchResults />}
      {page.kind === "gallery" && <FilteredGallery />}
      {page.kind === "pricing" && <PricingPlans />}
    </section>
  );
}
