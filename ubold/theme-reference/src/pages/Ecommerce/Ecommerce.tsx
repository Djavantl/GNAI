import PageHeader from "@/Components/Navigation/PageHeader/PageHeader";
import EcommerceDashboard from "@/pages/EcommerceDashboard/EcommerceDashboard";
import ProductCatalog from "@/Components/UI/ProductCatalog/ProductCatalog";
import ProductEditor from "@/Components/Forms/ProductEditor/ProductEditor";
import EcommerceOrders from "@/pages/EcommerceOrders/EcommerceOrders";
import EcommerceSellers from "@/pages/EcommerceSellers/EcommerceSellers";
import type { EcommercePageDefinition } from "./Ecommerce.types";
import "./Ecommerce.css";
export default function Ecommerce({
  page,
}: {
  page: EcommercePageDefinition;
}): JSX.Element {
  return (
    <section className="ecommercePage" aria-label={`eCommerce — ${page.title}`}>
      <PageHeader
        title={page.title}
        subtitle={
          page.kind === "dashboard"
            ? "Bem-vindo ao painel administrativo Ubold!"
            : undefined
        }
        breadcrumbItems={
          page.breadcrumb.length
            ? [
                ...page.breadcrumb.map((label) => ({ label, href: "#" })),
                {
                  label:
                    page.kind === "product-detail"
                      ? "Adicionar/editar"
                      : page.title,
                },
              ]
            : undefined
        }
      />
      {page.kind === "dashboard" ? (
        <EcommerceDashboard />
      ) : page.kind === "products" ? (
        <ProductCatalog />
      ) : page.kind === "product-detail" ? (
        <ProductEditor />
      ) : page.kind === "orders" ? (
        <EcommerceOrders />
      ) : (
        <EcommerceSellers />
      )}
    </section>
  );
}
