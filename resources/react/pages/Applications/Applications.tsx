import PageBreadcrumb from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";
import Mailbox from "@/components/UI/Mailbox/Mailbox";
import ContactDirectory from "@/components/UI/ContactDirectory/ContactDirectory";
import CalendarPage from "@/pages/CalendarPage/CalendarPage";
import type { ApplicationDefinition } from "./Applications.types";
import "./Applications.css";
export default function Applications({
  page,
}: {
  page: ApplicationDefinition;
}): React.JSX.Element {
  return (
    <section className="applicationsPage" aria-label={page.title}>
      <h4 className="applicationsPage__title">{page.title}</h4>
      <PageBreadcrumb
        items={[
          { label: "Interface", href: "#" },
          { label: "Aplicativos", href: "#" },
          { label: page.title },
        ]}
      />
      {page.kind === "contact" ? (
        <ContactDirectory />
      ) : page.kind === "calendar" ? (
        <CalendarPage />
      ) : (
        <Mailbox kind={page.kind} />
      )}
    </section>
  );
}
