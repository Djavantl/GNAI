import PageHeader from "@/Components/Navigation/PageHeader/PageHeader";
import type { LayoutDefinition } from "./Layouts.types";
import "./Layouts.css";

export default function Layouts({
  page,
}: {
  page: LayoutDefinition;
}): JSX.Element {
  return (
    <section className="layoutsPage" aria-label={page.title}>
      <PageHeader
        title={page.title}
        variant={page.header}
        breadcrumbItems={[
          { label: "Ubold", href: "#" },
          { label: "Layouts", href: "#" },
          { label: page.title },
        ]}
      />
      <div className="layoutsPage__spacer" aria-hidden="true" />
    </section>
  );
}
