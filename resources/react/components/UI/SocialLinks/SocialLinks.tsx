import { Fragment } from "react";
import Tooltip from "@/components/UI/Tooltip/Tooltip";
import { socialLinks } from "./SocialLinks.data";
import "./SocialLinks.css";
export default function SocialLinks(): React.JSX.Element {
  return (
    <ul className="socialLinks">
      {socialLinks.map((link) => (
        <Fragment key={link.icon}>
          <li>
            <Tooltip text={link.label}>
              <a href="" aria-label={link.label}>
                <i className={`fa fa-${link.icon}`} aria-hidden="true" />
              </a>
            </Tooltip>
          </li>{" "}
        </Fragment>
      ))}
    </ul>
  );
}
