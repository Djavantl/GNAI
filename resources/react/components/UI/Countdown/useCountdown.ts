import { useEffect, useState } from "react";
import type { CountdownValue } from "./Countdown.types";
export function calculateCountdown(
  endTime: number,
  now = Date.now(),
): CountdownValue {
  const remaining = Math.max(0, (endTime - now) / 1000);
  const years = Math.floor(remaining / 31557600);
  const withinYear = remaining - years * 31557600;
  return {
    days: years * 365 + Math.floor(withinYear / 86400),
    hours: Math.floor(withinYear / 3600) % 24,
    minutes: Math.floor(withinYear / 60) % 60,
    seconds: Math.round(withinYear % 60),
  };
}
export function useCountdown(endTime: number): CountdownValue {
  const [value, setValue] = useState<CountdownValue>(() =>
    calculateCountdown(endTime),
  );
  useEffect(() => {
    setValue(calculateCountdown(endTime));
    const timer = window.setInterval(
      () => setValue(calculateCountdown(endTime)),
      1000,
    );
    return () => window.clearInterval(timer);
  }, [endTime]);
  return value;
}
