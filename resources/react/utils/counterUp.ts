interface CounterToken {
  prefix: string;
  numeric: string;
  suffix: string;
}

function parseCounterToken(value: string): CounterToken {
  const match = value.match(/^([^0-9-]*)(-?[0-9][0-9,]*(?:\.[0-9]+)?)(.*)$/);

  if (!match) {
    return {
      prefix: '',
      numeric: value,
      suffix: ''
    };
  }

  return {
    prefix: match[1],
    numeric: match[2],
    suffix: match[3]
  };
}

function formatThousands(value: string): string {
  let formatted = value;

  while (/(\d+)(\d{3})/.test(formatted)) {
    formatted = formatted.replace(/(\d+)(\d{3})/, '$1,$2');
  }

  return formatted;
}

export function buildCounterFrames(value: string, time: number = 1200, delay: number = 100): string[] {
  const { prefix, numeric, suffix } = parseCounterToken(value);
  const stepCount = Math.max(1, Math.round(time / delay));
  const rawNumeric = numeric.replace(/,/g, '');
  const decimalMatch = rawNumeric.match(/\.(\d+)$/);
  const decimalPlaces = decimalMatch ? decimalMatch[1].length : 0;
  const numericValue = Number(rawNumeric);
  const useThousands = /[0-9]+,[0-9]+/.test(numeric);
  const frames: string[] = [];

  if (!Number.isFinite(numericValue)) {
    return [value];
  }

  for (let step = stepCount; step >= 1; step -= 1) {
    const currentValue = numericValue / stepCount * step;
    let formattedValue = decimalPlaces > 0
      ? currentValue.toFixed(decimalPlaces)
      : String(parseInt(String(currentValue), 10));

    if (useThousands) {
      formattedValue = formatThousands(formattedValue);
    }

    frames.unshift(`${prefix}${formattedValue}${suffix}`);
  }

  return frames;
}

export function getCounterStartValue(value: string): string {
  const { prefix, suffix } = parseCounterToken(value);

  return `${prefix}0${suffix}`;
}
