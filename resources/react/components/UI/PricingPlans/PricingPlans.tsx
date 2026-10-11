import PricingCard, { IconPricingCard } from "@/components/UI/PricingCard/PricingCard";
import { pricingPlans, iconPricingPlans } from "./PricingPlans.data";
import "./PricingPlans.css";
export default function PricingPlans(): React.JSX.Element {
  return (
    <>
      <div className="pricingPlans pricingPlans--first">
        {pricingPlans.map((plan) => (
          <div key={plan.name}>
            <PricingCard plan={plan} />
          </div>
        ))}
      </div>
      <div className="pricingPlans">
        {iconPricingPlans.map((plan) => (
          <div key={plan.name}>
            <IconPricingCard plan={plan} />
          </div>
        ))}
      </div>
    </>
  );
}
