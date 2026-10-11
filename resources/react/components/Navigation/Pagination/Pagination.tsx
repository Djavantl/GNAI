import "./Pagination.css";
export default function Pagination({
  large = false,
  split = false,
  currentPage = 2,
  pageCount = 5,
  onSelect,
  disabled = false,
}: {
  large?: boolean;
  split?: boolean;
  currentPage?: number;
  pageCount?: number;
  onSelect?: (page: number) => void;
  disabled?: boolean;
}): React.JSX.Element {
  const pages = Array.from(new Set([1, currentPage - 1, currentPage, currentPage + 1, pageCount]))
    .filter(page => page >= 1 && page <= pageCount).sort((a, b) => a - b);
  const visiblePages = pageCount <= 7 ? Array.from({ length: pageCount }, (_, i) => i + 1) : pages;
  const items = ['prev', ...visiblePages.flatMap((page, index) =>
    index > 0 && page - visiblePages[index - 1] > 1 ? [`gap-${page}`, String(page)] : [String(page)]
  ), 'next'];
  return (
    <ul
      className={`paginationControl${split ? " paginationControl--split" : ""}${large ? " paginationControl--lg paginationControl--last" : ""}`}
      aria-label="Paginação"
    >
      {items.map((item) => {
        const gap = item.startsWith('gap-');
        const unavailable = disabled || gap || (item === 'prev' && currentPage <= 1) || (item === 'next' && currentPage >= pageCount);
        return (
        <li
          key={item}
          className={
            unavailable
              ? "disabled"
              : item === String(currentPage)
                ? "active"
                : undefined
          }
        >
          <a
            href="#"
            aria-label={
              item === "prev"
                ? "Página anterior"
                : item === "next"
                  ? "Próxima página"
                  : gap ? "Mais páginas" : `Página ${item}`
            }
            aria-current={item === String(currentPage) ? "page" : undefined}
            aria-disabled={unavailable}
            tabIndex={unavailable ? -1 : undefined}
            onClick={(event) => {
              event.preventDefault();
              if (!unavailable)
                onSelect?.(
                  item === "next"
                    ? Math.min(pageCount, currentPage + 1)
                    : item === "prev" ? currentPage - 1 : Number(item),
                );
            }}
          >
            {item === "prev" ? (
              <i className="fa fa-angle-left" aria-hidden="true" />
            ) : item === "next" ? (
              <i className="fa fa-angle-right" aria-hidden="true" />
            ) : (
              gap ? "…" : item
            )}
          </a>
        </li>
        );
      })}
    </ul>
  );
}
