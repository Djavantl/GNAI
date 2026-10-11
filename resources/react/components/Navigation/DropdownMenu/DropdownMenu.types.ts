import type { ReactNode } from "react";
import type { ButtonSize, ButtonTone } from "@/components/UI/Button/Button";
export interface DropdownItem {
  label: string;
  href?: string;
  dividerBefore?: boolean;
  onSelect?: () => void;
}
export interface DropdownMenuProps {
  label: ReactNode;
  accessibleLabel?: string;
  tone?: ButtonTone;
  items: DropdownItem[];
  className?: string;
  size?: ButtonSize;
  wavesLight?: boolean;
}
