import Button from "@/Components/UI/Button/Button";
import type { PricingPlan } from "./PricingCard.types";
import "./PricingCard.css";
export default function PricingCard({
  plan,
  onSignup,
}: {
  plan: PricingPlan;
  onSignup?: () => void;
}): JSX.Element {
  return (
    <article className={`pricingCard pricingCard--${plan.tone}`}>
      <header>
        <span className="pricingCard__price">${plan.price}</span>
        <span className="pricingCard__name">{plan.name}</span>
      </header>
      <ul>
        {plan.features.map((feature, i) => (
          <li key={i}>{feature}</li>
        ))}
      </ul>
      <Button
        label="Cadastrar"
        tone={plan.tone}
        width="md"
        onClick={onSignup}
      />
    </article>
  );
}
export function IconPricingCard({
  plan,
  onSignup,
}: {
  plan: PricingPlan;
  onSignup?: () => void;
}): JSX.Element {
  return (
    <article className={`iconPricingCard iconPricingCard--${plan.tone}`}>
      <div className="iconPricingCard__inner">
        <div className="iconPricingCard__wrap">
          <div className="iconPricingCard__icon">
            <i className={plan.icon} aria-hidden="true" />
          </div>
          <div className="iconPricingCard__title">{plan.name}</div>
          <ul>
            {plan.features.map((feature, i) => (
              <li key={i}>{feature}</li>
            ))}
          </ul>
          <div className="iconPricingCard__price">
            <sup>$</sup>
            {plan.price}
          </div>
          <div className="iconPricingCard__period">por mês</div>
          <div className="iconPricingCard__button">
            <Button
              label="Cadastrar"
              tone={plan.tone}
              width="md"
              onClick={onSignup}
            />
          </div>
        </div>
      </div>
    </article>
  );
}
