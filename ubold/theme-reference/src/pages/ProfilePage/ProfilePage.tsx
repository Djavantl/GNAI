import { useRef } from "react";
import { useScrollReveal } from "@/hooks/useScrollReveal";
import CardBox from "@/Components/UI/CardBox/CardBox";
import ImageDisplay from "@/Components/UI/ImageDisplay/ImageDisplay";
import ProgressBar from "@/Components/UI/ProgressBar/ProgressBar";
import ActivityTimeline from "@/Components/UI/ActivityTimeline/ActivityTimeline";
import { profileActivity } from "@/Components/UI/ActivityTimeline/ActivityTimeline.data";
import { profileData as data } from "./ProfilePage.data";
import "./ProfilePage.css";
export default function ProfilePage(): JSX.Element {
  const host = useRef<HTMLDivElement>(null);
  const mobileDevice =
    /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
      navigator.userAgent,
    );
  useScrollReveal(
    host,
    ".profilePage__skill",
    ".progressBar__bar",
    1,
    50,
    !mobileDevice,
  );
  return (
    <div ref={host} className="profilePage">
      <header className="profilePage__banner">
        <ImageDisplay
          src={data.avatar}
          alt={`Foto de ${data.name}`}
          shape="thumbnail"
          size="lg"
        />
        <h4>
          <b>{data.name}</b>
        </h4>
        <p>
          <i className="fa fa-map-marker" aria-hidden="true" /> {data.location}
        </p>
      </header>
      <div className="profilePage__columns">
        <aside className="profilePage__sidebar">
          <CardBox className="profilePage__first">
            <h4 className="profilePage__heading">
              <b>Informações pessoais</b>
            </h4>
            <div className="profilePage__padding">
              {data.details.map((detail) => (
                <div className="profilePage__detail" key={detail.label}>
                  <strong>{detail.label}</strong>
                  <br />
                  <p>{detail.value}</p>
                </div>
              ))}
            </div>
          </CardBox>
          <CardBox>
            <h4 className="profilePage__heading">
              <b>Biografia</b>
            </h4>
            <div className="profilePage__padding">
              {data.biography.map((paragraph, i) => (
                <p key={i}>
                  {i === 1 ? <strong>{paragraph}</strong> : paragraph}
                </p>
              ))}
            </div>
          </CardBox>
          <CardBox>
            <h4 className="profilePage__heading">
              <b>Habilidades</b>
            </h4>
            <div className="profilePage__padding">
              {data.skills.map((skill) => (
                <div className="profilePage__skill" key={skill.name}>
                  <h5>
                    {skill.name}
                    <span>{skill.value}%</span>
                  </h5>
                  <ProgressBar
                    value={skill.value}
                    tone={skill.tone}
                    animated
                    label={
                      <span className="progressBar__srOnly">
                        {skill.value}% concluído
                      </span>
                    }
                  />
                </div>
              ))}
            </div>
          </CardBox>
        </aside>
        <div className="profilePage__content">
          <CardBox className="profilePage__first">
            <h4 className="profilePage__heading">
              <b>Atividades</b>
            </h4>
            <div className="profilePage__padding">
              <ActivityTimeline items={profileActivity} />
            </div>
          </CardBox>
          <CardBox>
            <h4 className="profilePage__heading profilePage__heading--office">
              <b>Meu escritório</b>
            </h4>
            <div className="profilePage__mapContainer">
              <iframe
                className="profilePage__map"
                height="300"
                src={data.mapSrc}
                title="Mapa do escritório Google India"
              />
            </div>
            <br />
            <div className="profilePage__office">
              <h4>
                <b>
                  <a href="#">Google India Pvt. Ltd</a>
                </b>
              </h4>
              <p>No. 3, RMZ Infinity - Tower E 3rd, 4th, and 5th Floors,</p>
              <p>Old Madras Road,</p>
              <p>Bengaluru, Karnataka 560016,</p>
              <p>Índia</p>
            </div>
            <div className="profilePage__clear" />
          </CardBox>
        </div>
      </div>
    </div>
  );
}
