import { useState } from "react";
import GalleryFilter from "@/Components/UI/GalleryFilter/GalleryFilter";
import { useIsotopeLayout } from "@/hooks/useIsotopeLayout";
import ImageLightbox from "@/Components/UI/ImageLightbox/ImageLightbox";
import ImageDisplay from "@/Components/UI/ImageDisplay/ImageDisplay";
import { galleryItems, galleryFilters } from "./FilteredGallery.data";
import type { GalleryItem } from "./FilteredGallery.types";
import "./FilteredGallery.css";
export default function FilteredGallery({
  items = galleryItems,
}: {
  items?: GalleryItem[];
}): JSX.Element {
  const [filter, setFilter] = useState("*");
  const host = useIsotopeLayout(filter, ".filteredGallery__item", items);
  return (
    <div className="filteredGallery">
      <GalleryFilter
        label="Categorias da galeria"
        items={galleryFilters}
        selected={filter}
        onSelect={setFilter}
      />
      <ImageLightbox>
        <div ref={host} className="filteredGallery__grid">
          {items.map((item) => (
            <div
              className={`filteredGallery__item ${item.categories.join(" ")}`}
              key={item.src}
            >
              <article>
                <a href={item.src} data-lightbox title={item.caption}>
                  <ImageDisplay src={item.src} alt={item.caption} />
                </a>
                <h4>{item.title}</h4>
              </article>
            </div>
          ))}
        </div>
      </ImageLightbox>
    </div>
  );
}
