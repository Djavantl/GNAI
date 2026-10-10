import Countdown from "@/Components/UI/Countdown/Countdown";
import RotatingText from "@/Components/UI/RotatingText/RotatingText";
import { comingSoonCopy as copy } from "./StatusPage.data";
import "./StatusPage.css";
export default function StatusPage({
  kind,
}: {
  kind: "maintenance" | "coming-soon";
}): JSX.Element {
  const maintenance = kind === "maintenance";
  return (
    <main
      className={`statusPage${maintenance ? " statusPage--maintenance" : ""}`}
      aria-label={maintenance ? "Manutenção" : "Em breve"}
    >
      <div className="statusPage__background" aria-hidden="true" />
      <div className="statusPage__container">
        <div className="statusPage__wrapper">
          <h1 className="statusPage__icon">
            <i className="md md-album" aria-hidden="true" />
          </h1>
          <h1 className="statusPage__title">
            {maintenance ? (
              <>
                <span>Site</span> <span>em</span> <span>manutenção</span>
              </>
            ) : (
              <RotatingText texts={copy.titles} />
            )}
          </h1>
          {maintenance ? (
            <h4>Estamos melhorando o sistema. Voltaremos em breve.</h4>
          ) : (
            <>
              <p>
                {copy.description[0]}
                <br />
                {copy.description[1]}
              </p>
              <div className="statusPage__countdown">
                <Countdown endTime={copy.endTime} />
              </div>
            </>
          )}
        </div>
      </div>
    </main>
  );
}
