import type { ReactNode } from "react";
import { joinClassNames } from "@/utils/joinClassNames";
import { useSidebarSubmenuHeight } from "./useSidebarSubmenuHeight";
import "./SidebarSubmenu.css";

interface SidebarSubmenuProps {
  children: ReactNode;
  className?: string;
  collapsed: boolean;
  isOpen: boolean;
  id?: string;
}

export default function SidebarSubmenu({
  children,
  className,
  collapsed,
  isOpen,
  id,
}: SidebarSubmenuProps): React.JSX.Element {
  const { ref, maxHeight, finishOpening } = useSidebarSubmenuHeight(
    isOpen,
    collapsed,
  );
  return (
    <ul
      ref={ref}
      id={id}
      className={joinClassNames(
        "list-unstyled",
        "sidebarPrimary__submenu",
        "sidebarSubmenu",
        isOpen && !collapsed && "sidebarSubmenu--open",
        collapsed && "sidebarSubmenu--collapsed",
        className,
      )}
      style={collapsed ? undefined : { maxHeight }}
      aria-hidden={collapsed || !isOpen}
      onTransitionEnd={(event) => {
        if (
          event.target === event.currentTarget &&
          event.propertyName === "max-height"
        )
          finishOpening();
      }}
    >
      {children}
    </ul>
  );
}
