import { useRef } from "react";
import { useScrollReveal } from "@/hooks/useScrollReveal";
import Button from "@/Components/UI/Button/Button";
import ImageDisplay from "@/Components/UI/ImageDisplay/ImageDisplay";
import { timelineEvents } from "./EventTimeline.data";
import type { TimelineEvent } from "./EventTimeline.types";
import "./EventTimeline.css";
export default function EventTimeline({
  events = timelineEvents,
}: {
  events?: TimelineEvent[];
}): JSX.Element {
  const host = useRef<HTMLElement>(null);
  useScrollReveal(
    host,
    ".cd-timeline-block",
    ".cd-timeline-img, .cd-timeline-content",
  );
  return (
    <div className="eventTimelineFrame">
      <section
        ref={host}
        className="eventTimeline cd-container"
        aria-label="Linha do tempo"
      >
        {events.map((event, i) => (
          <article className="cd-timeline-block" key={i}>
            <div className={`cd-timeline-img cd-${event.tone}`}>
              <i className={event.icon} aria-hidden="true" />
            </div>
            <div className="cd-timeline-content">
              <h3>{event.title}</h3>
              <p>
                {event.description}
                {event.action === "Leia mais" && (
                  <>
                    {" "}
                    <a href="#">{event.action}</a>
                  </>
                )}
              </p>
              {event.action && event.action !== "Leia mais" && (
                <Button
                  label={event.action}
                  tone="primary"
                  rounded
                  className="eventTimeline__action"
                />
              )}
              {event.image && (
                <ImageDisplay src={event.image} alt="Imagem do evento" />
              )}
              <span className="cd-date">{event.date}</span>
            </div>
          </article>
        ))}
      </section>
    </div>
  );
}
