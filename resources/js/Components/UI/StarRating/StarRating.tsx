import { Fragment } from "react";
import "./StarRating.css";
import type { RatingStar } from "./StarRating.types";
const icons: Record<RatingStar, string> = {
  full: "fa-star",
  half: "fa-star-half-o",
  empty: "fa-star-o",
};
export default function StarRating({
  stars,
}: {
  stars: RatingStar[];
}): JSX.Element {
  return (
    <ul className="starRating" aria-label="Avaliação do produto">
      {stars.map((star, index) => (
        <Fragment key={index}>
          {index > 0 ? " " : null}
          <li>
            <a
              href="#"
              className={`fa ${icons[star]}`}
              aria-label={`Estrela ${index + 1}: ${star === "full" ? "cheia" : star === "half" ? "meia" : "vazia"}`}
            />
          </li>
        </Fragment>
      ))}
    </ul>
  );
}
