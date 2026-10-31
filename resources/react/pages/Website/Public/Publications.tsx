import { router } from "@inertiajs/react";
import { useState, type FormEvent } from "react";
import Pagination from "@/components/Navigation/Pagination/Pagination";
import PublicationCard from "@/components/Website/PublicationCard/PublicationCard";
import SiteLayout from "@/components/Website/SiteLayout/SiteLayout";
import SitePageHeader from "@/components/Website/SitePageHeader/SitePageHeader";
import {
    websiteRoutes,
    type PublicationCardData,
    type WebsitePaginator,
    type WebsiteSeo,
    type WebsiteSite,
} from "@/components/Website/Website.types";
import "./WebsitePublic.css";

type Filters = { q: string; type: string; tag: string };
type Props = {
    site: WebsiteSite;
    publications: WebsitePaginator<PublicationCardData>;
    filters: Partial<Filters>;
    types: { value: string; label: string }[];
    tags: string[];
    seo: WebsiteSeo;
};

function cleanFilters(filters: Filters): Partial<Filters> {
    return Object.fromEntries(
        Object.entries(filters).filter(([, value]) => value.trim() !== ""),
    );
}

export default function WebsitePublicPublicationsPage({
    site,
    publications,
    filters: initialFilters,
    types,
    tags,
    seo,
}: Props): React.JSX.Element {
    const [filters, setFilters] = useState<Filters>({
        q: initialFilters.q ?? "",
        type: initialFilters.type ?? "",
        tag: initialFilters.tag ?? "",
    });
    const [loading, setLoading] = useState(false);
    const hasFilters = Object.values(filters).some((value) => value !== "");

    function visit(params: Partial<Filters> & { page?: number }): void {
        router.get(websiteRoutes.publications, params, {
            preserveState: true,
            preserveScroll: params.page === undefined,
            onStart: () => setLoading(true),
            onFinish: () => setLoading(false),
        });
    }

    function submit(event: FormEvent<HTMLFormElement>): void {
        event.preventDefault();
        visit(cleanFilters(filters));
    }

    function clear(): void {
        const empty = { q: "", type: "", tag: "" };
        setFilters(empty);
        visit({});
    }

    return (
        <SiteLayout site={site} current="publications" seo={seo}>
            <SitePageHeader
                eyebrow="Publicações"
                title="Notícias, eventos e comunicados"
                subtitle={`Acompanhe as novidades, os eventos e os registros do ${site.name}.`}
                trail={[{ label: "Publicações" }]}
            />
            <section className="websitePublic__section">
                <div className="siteLayout__container">
                    <form className="websitePublic__filters" role="search" onSubmit={submit}>
                        <div className="websitePublic__field websitePublic__field--wide">
                            <label htmlFor="publications-search">Buscar</label>
                            <input
                                id="publications-search"
                                type="search"
                                value={filters.q}
                                maxLength={120}
                                placeholder="Título ou resumo"
                                onChange={(event) => setFilters((current) => ({ ...current, q: event.target.value }))}
                            />
                        </div>
                        <div className="websitePublic__field">
                            <label htmlFor="publications-type">Tipo</label>
                            <select
                                id="publications-type"
                                value={filters.type}
                                onChange={(event) => setFilters((current) => ({ ...current, type: event.target.value }))}
                            >
                                <option value="">Todos os tipos</option>
                                {types.map((type) => (
                                    <option key={type.value} value={type.value}>{type.label}</option>
                                ))}
                            </select>
                        </div>
                        {tags.length ? (
                            <div className="websitePublic__field">
                                <label htmlFor="publications-tag">Etiqueta</label>
                                <select
                                    id="publications-tag"
                                    value={filters.tag}
                                    onChange={(event) => setFilters((current) => ({ ...current, tag: event.target.value }))}
                                >
                                    <option value="">Todas as etiquetas</option>
                                    {tags.map((tag) => (
                                        <option key={tag} value={tag}>{tag}</option>
                                    ))}
                                </select>
                            </div>
                        ) : null}
                        <div className="websitePublic__filterActions">
                            <button type="submit" className="websitePublic__button" disabled={loading}>
                                <i className="fa fa-search" aria-hidden="true" />
                                {loading ? "Filtrando..." : "Filtrar"}
                            </button>
                            {hasFilters ? (
                                <button
                                    type="button"
                                    className="websitePublic__button websitePublic__button--ghost"
                                    disabled={loading}
                                    onClick={clear}
                                >
                                    Limpar
                                </button>
                            ) : null}
                        </div>
                    </form>
                    <p className="websitePublic__count" role="status" aria-live="polite">
                        {publications.total === 1
                            ? "1 publicação encontrada"
                            : `${publications.total} publicações encontradas`}
                    </p>
                    <div aria-busy={loading}>
                        {publications.data.length ? (
                            <div className="websitePublic__grid">
                                {publications.data.map((publication) => (
                                    <PublicationCard key={publication.id} publication={publication} headingLevel={2} />
                                ))}
                            </div>
                        ) : (
                            <p className="websitePublic__empty">
                                {hasFilters
                                    ? "Nenhuma publicação encontrada com os filtros informados."
                                    : "Nenhuma publicação disponível no momento."}
                            </p>
                        )}
                    </div>
                    {publications.last_page > 1 ? (
                        <nav className="websitePublic__pagination" aria-label="Páginas de publicações">
                            <span>
                                Mostrando {publications.from} a {publications.to} de {publications.total}
                            </span>
                            <Pagination
                                currentPage={publications.current_page}
                                pageCount={publications.last_page}
                                disabled={loading}
                                onSelect={(page) => visit({ ...cleanFilters(filters), page })}
                            />
                        </nav>
                    ) : null}
                </div>
            </section>
        </SiteLayout>
    );
}
