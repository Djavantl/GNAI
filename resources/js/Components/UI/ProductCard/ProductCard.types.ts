import type { RatingStar } from "@/Components/UI/StarRating/StarRating.types";
export interface ProductRecord {
  id: string;
  image: string;
  name: string;
  category: "mobiles" | "tablets" | "desktops" | "other";
  stars: RatingStar[];
  price: string;
  stock: number;
}
