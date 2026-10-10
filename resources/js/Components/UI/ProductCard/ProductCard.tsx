import ImageDisplay from "@/Components/UI/ImageDisplay/ImageDisplay";
import Button from "@/Components/UI/Button/Button";
import StarRating from "@/Components/UI/StarRating/StarRating";
import type { ProductRecord } from "./ProductCard.types";
import "./ProductCard.css";
export default function ProductCard({
  product,
}: {
  product: ProductRecord;
}): JSX.Element {
  return (
    <article className="productCard">
      <a href={product.image} data-lightbox title="Imagem do produto">
        <ImageDisplay src={product.image} alt={product.name} />
      </a>
      <div className="productCard__actions">
        <Button
          href="#"
          tone="success"
          size="sm"
          waves={false}
          aria-label={`Editar ${product.name}`}
        >
          <i className="md md-mode-edit" />
        </Button>{" "}
        <Button
          href="#"
          tone="danger"
          size="sm"
          waves={false}
          aria-label={`Excluir ${product.name}`}
        >
          <i className="md md-close" />
        </Button>
      </div>
      <div className="productCard__detail">
        <h4>
          <a href="">{product.name}</a>
        </h4>
        <StarRating stars={product.stars} />
        <h5>
          <span className="productCard__price">{product.price}</span>{" "}
          <span className="productCard__stock">
            Estoque: {product.stock} un.
          </span>
        </h5>
      </div>
    </article>
  );
}
