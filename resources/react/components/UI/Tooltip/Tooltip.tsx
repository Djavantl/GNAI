import {
  cloneElement,
  useId,
  useLayoutEffect,
  useRef,
  useState,
  type ReactElement,
  type CSSProperties,
} from "react";
import { createPortal } from "react-dom";
import "./Tooltip.css";
export default function Tooltip({
  text,
  children,
}: {
  text: string;
  children: ReactElement;
}): React.JSX.Element {
  const id = useId();
  const trigger = useRef<HTMLSpanElement>(null);
  const overlay = useRef<HTMLSpanElement>(null);
  const [hovered, setHovered] = useState(false);
  const [focused, setFocused] = useState(false);
  const [position, setPosition] = useState<CSSProperties>({});
  const open = hovered || focused;
  useLayoutEffect(() => {
    if (!open) return;
    function update(): void {
      if (!trigger.current || !overlay.current) return;
      const rect = trigger.current.getBoundingClientRect();
      const width = overlay.current.offsetWidth;
      const left = Math.max(
        width / 2 + 5,
        Math.min(window.innerWidth - width / 2 - 5, rect.left + rect.width / 2),
      );
      setPosition({ left, top: rect.top - 3 });
    }
    update();
    window.addEventListener("scroll", update, true);
    window.addEventListener("resize", update);
    return () => {
      window.removeEventListener("scroll", update, true);
      window.removeEventListener("resize", update);
    };
  }, [open]);
  return (
    <span
      ref={trigger}
      className="legacyTooltip__trigger"
      onMouseEnter={() => setHovered(true)}
      onMouseLeave={() => setHovered(false)}
      onFocus={() => setFocused(true)}
      onBlur={() => setFocused(false)}
    >
      {cloneElement(children as ReactElement<{ "aria-describedby"?: string }>, {
        "aria-describedby": open ? id : undefined,
      })}
      {open &&
        createPortal(
          <span
            ref={overlay}
            id={id}
            role="tooltip"
            className="legacyTooltip"
            style={position}
          >
            <span className="legacyTooltip__inner">{text}</span>
            <span className="legacyTooltip__arrow" />
          </span>,
          document.body,
        )}
    </span>
  );
}
