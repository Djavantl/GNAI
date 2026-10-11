import "./Pagination.css";
export default function Pagination({
  large = false,
  split = false,
  currentPage = 2,
  pageCount = 5,
  onSelect,
}: {
  large?: boolean;
  split?: boolean;
  currentPage?: number;
  pageCount?: number;
  onSelect?: (page: number) => void;
}): React.JSX.Element {
  const items = [
    "prev",
    ...Array.from({ length: pageCount }, (_, i) => String(i + 1)),
    "next",
  ];
  return (
    <ul
      className={`paginationControl${split ? " paginationControl--split" : ""}${large ? " paginationControl--lg paginationControl--last" : ""}`}
      aria-label="Paginação"
    >
      {items.map((item) => (
        <li
          key={item}
          className={
            item === "prev"
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
                  : `Página ${item}`
            }
            aria-current={item === String(currentPage) ? "page" : undefined}
            aria-disabled={item === "prev"}
            onClick={(event) => {
              event.preventDefault();
              if (item !== "prev")
                onSelect?.(
                  item === "next"
                    ? Math.min(pageCount, currentPage + 1)
                    : Number(item),
                );
            }}
          >
            {item === "prev" ? (
              <i className="fa fa-angle-left" aria-hidden="true" />
            ) : item === "next" ? (
              <i className="fa fa-angle-right" aria-hidden="true" />
            ) : (
              item
            )}
          </a>
        </li>
      ))}
    </ul>
  );
}
