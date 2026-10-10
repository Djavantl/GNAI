import { useId } from "react";
import { useDropdownMenu } from "@/hooks/useDropdownMenu";
import ImageDisplay from "@/Components/UI/ImageDisplay/ImageDisplay";
import { sidebarUserActions } from "./SidebarUser.data";
import type { SidebarUserProps } from "./SidebarUser.types";
import "./SidebarUser.css";

export default function SidebarUser({ user }: SidebarUserProps): JSX.Element {
  const { host, open, toggle, close, onKeyDown } = useDropdownMenu();
  const id = useId();

  return (
    <div className="sidebarUser">
      <ImageDisplay
        className="sidebarUser__avatar"
        src={user.avatar}
        alt={user.name}
        shape="circle"
        size="md"
      />
      <div className="sidebarUser__info">
        <div ref={host} className="sidebarUser__dropdown" onKeyDown={onKeyDown}>
          <a
            href="#"
            className="sidebarUser__toggle"
            aria-haspopup="menu"
            aria-expanded={open}
            aria-controls={id}
            onClick={(event) => {
              event.preventDefault();
              toggle();
            }}
          >
            {user.name} <span className="sidebarUser__caret" />
          </a>
          <ul className="sidebarUser__menu" id={id} role="menu" hidden={!open}>
            {sidebarUserActions.map((action) => (
              <li key={action.label} role="none">
                <a
                  href="#"
                  role="menuitem"
                  onClick={(event) => {
                    event.preventDefault();
                    close();
                  }}
                >
                  <i className={action.icon} aria-hidden="true" />{" "}
                  {action.label}
                </a>
              </li>
            ))}
          </ul>
        </div>
        <p className="sidebarUser__role">{user.role}</p>
      </div>
    </div>
  );
}
