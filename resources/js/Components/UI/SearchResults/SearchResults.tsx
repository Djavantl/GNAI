import Tabs, { type TabItem } from "@/Components/Navigation/Tabs/Tabs";
import Badge from "@/Components/UI/Badge/Badge";
import MediaObject from "@/Components/UI/MediaObject/MediaObject";
import Pagination from "@/Components/Navigation/Pagination/Pagination";
import SearchInput from "@/Components/UI/SearchInput/SearchInput";
import SearchResultItem from "@/Components/UI/SearchResultItem/SearchResultItem";
import { searchData } from "./SearchResults.data";
import "./SearchResults.css";
export default function SearchResults(): JSX.Element {
  const items: TabItem[] = [
    {
      id: "all",
      accessibleLabel: "Todos os resultados",
      label: (
        <>
          <b>Todos os resultados</b>
          <Badge variant="primary">77</Badge>
        </>
      ),
      icon: "fa fa-home",
      content: (
        <>
          {searchData.results.map((result, i) => (
            <SearchResultItem key={i} result={result} />
          ))}
          <div className="searchResults__pagination">
            <Pagination />
          </div>
        </>
      ),
    },
    {
      id: "users",
      accessibleLabel: "Usuários",
      label: (
        <>
          <b>Usuários</b>
          <Badge variant="pink">9</Badge>
        </>
      ),
      icon: "fa fa-user",
      content: (
        <>
          {searchData.users.map((user) => (
            <article className="searchResults__user" key={user.name}>
              <MediaObject
                heading={<a href="#">{user.name}</a>}
                leftImageSrc={user.avatar}
                leftImageAlt={`Foto de ${user.name}`}
              >
                <p>
                  <b>E-mail:</b>
                  <br />
                  <a href="#" className="searchResults__muted">
                    {user.email}
                  </a>
                </p>
                <p>
                  <b>Biografia:</b>
                  <br />
                  <span className="searchResults__muted">{user.bio}</span>
                </p>
              </MediaObject>
            </article>
          ))}
          <div className="searchResults__pagination">
            <Pagination />
          </div>
        </>
      ),
    },
    {
      id: "other",
      accessibleLabel: "Outros resultados",
      label: (
        <>
          <b>Outros resultados</b>
          <Badge variant="info">16</Badge>
        </>
      ),
      icon: "fa fa-user",
      content: (
        <>
          {searchData.other.map((text, i) => (
            <p key={i}>{text}</p>
          ))}
        </>
      ),
    },
  ];
  return (
    <div className="searchResults">
      <div className="searchResults__search">
        <SearchInput />
      </div>
      <div className="searchResults__heading">
        <h3>
          <b>Resultados da busca por "Painel administrativo"</b>
        </h3>
      </div>
      <Tabs items={items} variant="bootstrap" />
    </div>
  );
}
