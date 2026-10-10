import { useEffect, useMemo, useState } from 'react';

const COUNTER_UP_DELAY_MS = 100;
const COUNTER_UP_TIME_MS = 1200;
const COUNTER_UP_STEPS = Math.max(1, Math.ceil(COUNTER_UP_TIME_MS / COUNTER_UP_DELAY_MS));

export default function AnimatedCounter({ className, value, wrapper = 'b' }: { className?: string; value: number; wrapper?: 'b' | 'span' }): JSX.Element {
  const values = useMemo((): number[] => {
    const list: number[] = [];
    for (let step = 1; step <= COUNTER_UP_STEPS; step += 1) {
      list.push(Math.ceil((value * step) / COUNTER_UP_STEPS));
    }
    return list;
  }, [value]);

  const [displayValue, setDisplayValue] = useState<number>(0);

  useEffect(() => {
    setDisplayValue(0);
    let step = 0;
    const timer = window.setInterval(() => {
      if (step >= values.length) {
        setDisplayValue(value);
        window.clearInterval(timer);
        return;
      }
      setDisplayValue(values[step]);
      step += 1;
    }, COUNTER_UP_DELAY_MS);

    return () => window.clearInterval(timer);
  }, [value, values]);

  const CounterTag = wrapper;

  return <CounterTag className={className}>{displayValue}</CounterTag>;
}
