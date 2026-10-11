import type { ReactNode } from "react";
export interface ScrollAreaProps {
  children: ReactNode;
  height?: number;
  maxHeight?: number;
  className?: string;
  label: string;
}
