import { useCountdown } from "./useCountdown";
import "./Countdown.css";
export default function Countdown({
  endTime,
}: {
  endTime: number;
}): JSX.Element {
  const time = useCountdown(endTime);
  const units = [
    { value: String(time.days), label: "Dias", tone: "custom" },
    {
      value: String(time.hours).padStart(2, "0"),
      label: "Horas",
      tone: "primary",
    },
    {
      value: String(time.minutes).padStart(2, "0"),
      label: "Minutos",
      tone: "pink",
    },
    {
      value: String(time.seconds).padStart(2, "0"),
      label: "Segundos",
      tone: "info",
    },
  ];
  return (
    <div className="countdown" aria-label="Contagem regressiva">
      {[units.slice(0, 2), units.slice(2)].map((group, i) => (
        <div key={i}>
          {group.map((unit) => (
            <div key={unit.label}>
              <span
                className={`countdown__value countdown__value--${unit.tone}`}
              >
                {unit.value}
              </span>
              <span>
                <b>{unit.label}</b>
              </span>
            </div>
          ))}
        </div>
      ))}
    </div>
  );
}
