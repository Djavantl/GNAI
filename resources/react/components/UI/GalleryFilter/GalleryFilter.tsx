import { Fragment } from "react";
import "./GalleryFilter.css";

import type { GalleryFilterItem } from "./GalleryFilter.types";
export default function GalleryFilter({
  items,
  selected,
  onSelect,
  label,
}: {
  items: GalleryFilterItem[];
  selected: string;
  onSelect: (filter: string) => void;
  label: string;
}): React.JSX.Element {
  return (
    <nav className="galleryFilter" aria-label={label}>
      {items.map((item, index) => (
        <Fragment key={item.id}>
          {index > 0 ? " " : null}
          <a
            href="#"
            className={selected === item.id ? "current" : undefined}
            aria-current={selected === item.id ? "true" : undefined}
            onClick={(event) => {
              event.preventDefault();
              onSelect(item.id);
            }}
          >
            {item.label}
          </a>
        </Fragment>
      ))}
    </nav>
  );
}
