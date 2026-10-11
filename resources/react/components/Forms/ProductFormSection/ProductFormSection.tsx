import type { ReactNode } from "react";
import CardBox from "@/components/UI/CardBox/CardBox";
import "./ProductFormSection.css";
export default function ProductFormSection({
  title,
  children,
  images = false,
}: {
  title: string;
  children: ReactNode;
  images?: boolean;
}): React.JSX.Element {
  return (
    <CardBox>
      <h5
        className={`productFormSection__title${images ? " productFormSection__title--images" : ""}`}
      >
        <b>{title}</b>
      </h5>
      {children}
    </CardBox>
  );
}
