import type { ButtonTone } from "@/Components/UI/Button/Button";
export interface PricingPlan {
  name: string;
  price: number;
  tone: ButtonTone;
  features: string[];
  icon?: string;
}
