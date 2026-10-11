import { useState } from "react";
import GalleryFilter from "@/components/UI/GalleryFilter/GalleryFilter";
import ImageLightbox from "@/components/UI/ImageLightbox/ImageLightbox";
import ProductCard from "@/components/UI/ProductCard/ProductCard";
import { useIsotopeLayout } from "@/hooks/useIsotopeLayout";
import { products, productFilters } from "./ProductCatalog.data";
import "./ProductCatalog.css";
export default function ProductCatalog(): React.JSX.Element {
  const [filter, setFilter] = useState("*");
  const host = useIsotopeLayout(filter, ".productCatalog__item", products);
  return (
    <div className="productCatalog">
      <GalleryFilter
        label="Categorias de produtos"
        items={productFilters}
        selected={filter}
        onSelect={setFilter}
      />
      <ImageLightbox>
        <div ref={host} className="productCatalog__grid">
          {products.map((product) => (
            <div
              key={product.id}
              className={`productCatalog__item ${product.category}`}
            >
              <ProductCard product={product} />
            </div>
          ))}
        </div>
      </ImageLightbox>
    </div>
  );
}
