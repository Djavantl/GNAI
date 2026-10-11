import PageHeader from "@/components/Navigation/PageHeader/PageHeader";
import CrmDashboard from "@/pages/CrmDashboard/CrmDashboard";
import CrmContacts from "@/pages/CrmContacts/CrmContacts";
import CrmOpportunities from "@/pages/CrmOpportunities/CrmOpportunities";
import CrmLeads from "@/pages/CrmLeads/CrmLeads";
import type { CrmPageDefinition } from "./Crm.types";
import "./Crm.css";
export default function Crm({
  page,
}: {
  page: CrmPageDefinition;
}): React.JSX.Element {
  return (
    <section className="crmPage" aria-label={`CRM — ${page.title}`}>
      <PageHeader
        title={page.title}
        subtitle={
          page.kind === "dashboard"
            ? "Bem-vindo ao painel administrativo Interface!"
            : undefined
        }
        breadcrumbItems={
          page.breadcrumb
            ? [
                { label: "Interface", href: "#" },
                { label: page.breadcrumb, href: "#" },
                { label: page.title },
              ]
            : undefined
        }
      />
      {page.kind === "dashboard" ? (
        <CrmDashboard />
      ) : page.kind === "opportunities" ? (
        <CrmOpportunities />
      ) : page.kind === "leads" ? (
        <CrmLeads />
      ) : (
        <CrmContacts customers={page.kind === "customers"} />
      )}
    </section>
  );
}
