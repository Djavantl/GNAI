import type { PricingPlan } from "@/components/UI/PricingCard/PricingCard.types";
export const pricingPlans: PricingPlan[] = [
  {
    name: "Plano inicial",
    price: 19,
    tone: "primary",
    features: [
      "5 projetos",
      "1 GB de espaço",
      "Sem domínio",
      "1 usuário",
      "Suporte 24x7",
    ],
  },
  {
    name: "Plano profissional",
    price: 29,
    tone: "pink",
    features: [
      "15 projetos",
      "5 GB de espaço",
      "2 domínios",
      "10 usuários",
      "Suporte 24x7",
    ],
  },
  {
    name: "Plano empresarial",
    price: 39,
    tone: "purple",
    features: [
      "Projetos ilimitados",
      "10 GB de espaço",
      "10 domínios",
      "100 usuários",
      "Suporte 24x7",
    ],
  },
  {
    name: "Plano ilimitado",
    price: 49,
    tone: "success",
    features: [
      "Projetos ilimitados",
      "100 GB de espaço",
      "50 domínios",
      "Usuários ilimitados",
      "Suporte 24x7",
    ],
  },
];
export const iconPricingPlans: PricingPlan[] = pricingPlans.map((plan, i) => ({
  ...plan,
  price: [99, 199, 299, 399][i],
  tone: (["success", "purple", "pink", "warning"] as const)[i],
  icon: ["ion-plane", "ion-trophy", "ion-umbrella", "ion-paper-airplane"][i],
  features: [
    "5 projetos",
    "1 GB de espaço",
    "Sem domínio",
    "1 usuário",
    "Suporte gratuito",
  ],
}));
