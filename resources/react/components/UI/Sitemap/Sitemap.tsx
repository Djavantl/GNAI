import CardBox from "@/components/UI/CardBox/CardBox";
import { sitemapColumns } from "./Sitemap.data";
import "./Sitemap.css";
export default function Sitemap(): React.JSX.Element {
  return (
    <CardBox>
      <h4 className="sitemap__heading">
        <b>Mapa do site</b>
      </h4>
      <p className="sitemap__description">
        Seu texto de apresentação vai aqui...
      </p>
      <div className="sitemap">
        {sitemapColumns.map((column, i) => (
          <div className="sitemap__column" key={i}>
            {column.map((group, j) => (
              <section key={j}>
                <h5>
                  <a href="#">
                    <i className={group.icon} aria-hidden="true" />
                    {group.title}
                  </a>
                </h5>
                <ul>
                  {group.links.map((link, k) => (
                    <li key={k}>
                      <a href={link.href}>{link.label}</a>
                    </li>
                  ))}
                </ul>
              </section>
            ))}
          </div>
        ))}
      </div>
    </CardBox>
  );
}
