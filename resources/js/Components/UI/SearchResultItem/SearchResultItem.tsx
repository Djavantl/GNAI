import "./SearchResultItem.css";
import type { SearchResult } from "./SearchResultItem.types";
export default function SearchResultItem({
  result,
}: {
  result: SearchResult;
}): JSX.Element {
  return (
    <article className="searchResultItem">
      <h3>
        <a href="#">{result.title}</a>
      </h3>
      <div className="searchResultItem__url">{result.url}</div>
      <p>{result.description}</p>
    </article>
  );
}
