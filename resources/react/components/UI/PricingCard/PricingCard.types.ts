import type { ButtonTone } from "@/components/UI/Button/Button";
export interface PricingPlan {
  name: string;
  price: number;
  tone: ButtonTone;
  features: string[];
  icon?: string;
}
